<?php

/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\KitPagesBundle\Controller;

use Kitpages\CmsBundle\Controller\PageController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kitpages\CmsBundle\Entity\Page;
use Kitpages\CmsBundle\Model\Paginator;

class PageController extends BaseController
{

    /**
     * @param \Kitpages\CmsBundle\Entity\Page $page
     * @param string $lang
     * @param string $urlTitle
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function viewAction(Page $page, $urlTitle)
    {
        $lang = $this->get('request')->attributes->get('_locale');
        $em = $this->getDoctrine()->getEntityManager();
        $context = $this->get('kitpages.cms.controller.context');
        $rendererTwig = $this->container->getParameter('kitpages_cms.page.renderer_twig_main');
        $pageId = $page->getId();
        $pageType = $page->getPageType();
        $pageLanguage = $page->getLanguage();
        $pageUrlTitle = $page->getUrlTitle();
        $pageLayout = $page->getLayout();
        $forcedUrl = $page->getForcedUrl();
        $data = array();

        if ($context->getViewMode() == Context::VIEW_MODE_PROD) {
            $pagePublish = $em->getRepository('KitpagesCmsBundle:PagePublish')->findByPage($page);
            if ($pagePublish == null ) {
                throw new NotFoundHttpException('The page does not exist.');
            }
            $pageType = $pagePublish->getPageType();
            $pageLanguage = $pagePublish->getLanguage();
            $pageUrlTitle = $pagePublish->getUrlTitle();
            $pageLayout = $pagePublish->getLayout();
            $forcedUrl = $pagePublish->getForcedUrl();
            $data = $pagePublish->getData();
        } else {
            $dataInheritanceList = $this->container->getParameter('kitpages_cms.page.data_inheritance_list');
            $dataRoot = $em->getRepository('KitpagesCmsBundle:Page')->getDataWithInheritance($page, $dataInheritanceList);
            $data['root'] = $dataRoot;
            $data['page'] = $page->getDataPage();
            $cmsFileManager = $cmsFileManager = $this->get('kitpages.cms.manager.file');
            $listMedia = $cmsFileManager->mediaList($data['root'], false);
            $data['media'] = $listMedia;
        }

        if ($pageType == "technical") {
            throw new NotFoundHttpException('The page does not exist.');
        }

        if ($pageType == "link") {
            return $this->redirect ($page->getLinkUrl(), 301);
        }

        if ($forcedUrl && ($forcedUrl != $this->getRequest()->getPathInfo() ) ) {
            return $this->redirect(
                $this->getRequest()->getBaseUrl().$forcedUrl
            );
        }

        if ( ($pageLanguage != $lang) || ($pageUrlTitle != $urlTitle) ) {
            return $this->redirect (
                $this->generateUrl(
                    'kitpages_cms_page_view_lang',
                    array(
                        'id' => $pageId,
                        '_locale' => $pageLanguage,
                        'urlTitle' => $pageUrlTitle
                    )
                ),
                301
            );
        }

        $cmsManager = $this->get('kitpages.cms.model.cmsManager');
        $layout = $this->container->getParameter('kitpages_cms.page.layout_list.'.$pageLayout);
        $cmsManager->setLayout($layout['renderer_twig']);

        $paginator = new Paginator();
        $pageNumber = $this->get('request')->query->get('page', 1);
        if ($pageNumber < 1) $pageNumber = 1;
        $paginator->setCurrentPage( $pageNumber );
        $paginator->setItemCountPerPage(10);
        $url = $page->getForcedUrl() != null?$page->getForcedUrl():'/'.$page->getId().'/'.$page->getUrlTitle();
        $paginator->setUrlTemplate($url.'?page=_PAGE_');
     
        $data['paginator'] = $paginator;
        return $this->render(
            $layout['renderer_twig'],
            array(
                'kitCmsViewMode' => $context->getViewMode(),
                'kitCmsPage' => $page,
                'kitCmsPageData' => $data
            )
        );
    }
    
    public function editAction(Request $request, Page $page, $inToolbar = false, $target = null)
    {
        if (is_null($target)) {
            $target = $request->query->get('kitpages_target', null);
        }

        if (!$page->getData()) {
            $page->setData(array('root'=>null));
        }
        $layout = $this->container->getParameter('kitpages_cms.page.layout_list.'.$page->getLayout());

        // build basic form
        $pageParent = $page->getParent();
        $parentId = '';
        if ($pageParent instanceof Page) {
            $parentId = $pageParent->getId();
        }

        // build custom form
        if (isset($layout['data_form_class'])) {
            $className = $layout['data_form_class'];
            $formData = new $className();
        } else {
            $formData = $this->get($layout['data_form_service']);
        }

        $form = $this->createForm(
            'kitpagesCmsEditPage',
            $page,
            array(
                'formTypeCustom' => $formData
            )
        );
        $form->get('parent_id')->setData($parentId);

        $formHandler = $this->container->get('kitpages_cms.formHandler.editPage');

        $process = $formHandler->process($form, $page);
        if (isset($process['msg'])) {
            if ($process['result']) {
                $this->get('session')->getFlashBag()->add('notice', $process['msg']);
            } else {
                $this->get('session')->getFlashBag()->add('error', $process['msg']);
            }

            if (is_null($target)) {
                if ($process['forcedUrl'] != null) {
                    return $this->redirect($process['forcedUrl']);
                } else {
                    return $this->redirect($this->generateUrl(
                        'kitpages_cms_page_view_lang',
                        array(
                            'id' => $page->getId(),
                            '_locale' => $page->getLanguage(),
                            'urlTitle' => $page->getUrlTitle()
                        )
                    ));
                }
            } else {
                return $this->redirect($target);
            }
        }

        return $this->render($layout['data_form_twig'], array(
            'form' => $form->createView(),
            'id' => $page->getId(),
            'inToolbar' => $inToolbar,
            'kitpages_target' => $target
        ));
    }
}
