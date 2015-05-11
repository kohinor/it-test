<?php
namespace App\KitPagesBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\KitPagesBundle\Controller\Context;
use Kitpages\CmsBundle\Entity\NavPublish;
use Kitpages\CmsBundle\Entity\Page;
use Kitpages\CmsBundle\Controller\NavController as BaseController;

class NavController extends BaseController
{    
    const CACHE_TIME = 21600;
    
    public function widgetAction($slug, $cssClass, $currentPageSlug, $startDepth = 1, $endDepth = 10, $filterByCurrentPage = true, $renderer = 'KitpagesCmsBundle:Nav:navigation.html.twig') {
        $em = $this->getDoctrine()->getManager();
        $context = $this->get('kitpages.cms.controller.context');
        $resultingHtml = '';
        $navigation = array();
        $selectPageSlugList = array();
        if ($startDepth == 1) {
           $filterByCurrentPage = false;
        }
        $page = $em->getRepository('KitpagesCmsBundle:Page')->findOneBySlug($slug);
        $this->get('logger')->info('slug = '.$slug);
        $currentPage = $em->getRepository('KitpagesCmsBundle:Page')->findOneBySlug($currentPageSlug);
        if ( (!$filterByCurrentPage) || ($currentPage != null) ) {
            if ($filterByCurrentPage && $currentPage != null) {
                $page = $em->getRepository('KitpagesCmsBundle:Page')->childOfPageWithForParentOtherPage($page, $currentPage, $startDepth-1);
                $startDepth = 1;
            }
            if ($page != null) {
                $startLevel = $page->getLevel() + $startDepth;
                $endLevel = $page->getLevel() + $endDepth;
                
                $key =  $slug.$this->getRequest()->getLocale();
                $cache   = $this->container->get('doctrine_cache.providers.memcached');
                $navigation = $cache->fetch($key);
                if (!$navigation) {
                    $navigation = $this->navPageChildren($page, $context->getViewMode(), $startDepth, $endLevel);
                    $cache->save($key, $navigation, self::CACHE_TIME);
                }
                
                if ($currentPage != null) {
                    $selectParentPageList = $em->getRepository('KitpagesCmsBundle:Page')->parentBetweenTwoDepth($currentPage, $startLevel, $endLevel);
                    foreach($selectParentPageList as $selectParentPage) {
                        $selectPageSlugList[] = $selectParentPage->getSlug();
                    }
                }
            }
        }
        return $this->render(
            $renderer,
            array(
                'currentPageSlug' => $currentPageSlug,
                'selectPageSlugList' => $selectPageSlugList,
                'navigation' => $navigation,
                'navigationSlug' => $slug,
                'navigationCssClass' => $cssClass,
                'root' => true,
                'kitCmsViewMode' => $context->getViewMode(),
                'kitpages_target' => $_SERVER["REQUEST_URI"]
            )
        );
    }
    
    public function treeChildren($pageParent = null){
        $em = $this->getDoctrine()->getManager();

        if (is_null($pageParent)) {
            $pageList = $em->getRepository('KitpagesCmsBundle:Page')->getRootNodes();
        } else {
            $pageList = $em->getRepository('KitpagesCmsBundle:Page')->children($pageParent, true);
        }

        $pageListRenderer = array();
        foreach($pageList as $page) {
            $pageTree = array();
            $pageTree['id'] = $page->getId();
            $pageTree['slug'] = $page->getSlug();
            $pageTree['menuTitle'] = $page->getMenuTitle();
            $pageTree['isPublished'] = $page->getIsPublished();
            $paramUrl = array(
                'id' => $page->getId(),
                'kitpages_target' => $_SERVER["REQUEST_URI"]
            );
            $paramUrlCreate = array(
                'parent_id' => $page->getId(),
                'kitpages_target' => $_SERVER["REQUEST_URI"]
            );
            $paramUrlWithChild = array(
                'id' => $page->getId(),
                'children' => true,
                'kitpages_target' => $_SERVER["REQUEST_URI"]
            );


            if ($page->getIsPendingDelete() == 1) {
                if ($pageParent->getIsPendingDelete() == 0) {
                    $pageTree['actionList'][] = array(
                        'id' => '',
                        'label' => 'undelete',
                        'url' => $this->generateUrl('kitpages_cms_page_undelete', $paramUrl),
                    );
                    $pageTree['actionList'][] = array(
                        'id' => '',
                        'label' => 'confirm delete',
                        'url' => $this->generateUrl('kitpages_cms_page_publish', $paramUrlWithChild),
                        'class' => 'kit-cms-modal-open'
                    );
                } else {
                    $pageTree['actionList'][] = array(
                        'id' => '',
                        'label' => 'publish all',
                        'url' => $this->generateUrl('kitpages_cms_page_publish', $paramUrlWithChild),
                        'class' => 'kit-cms-advanced'
                    );
                }
            } else {

                $pageTree['actionList'][] = array(
                    'id' => 'publish',
                    'label' => 'publish',
                    'url'  => $this->generateUrl('kitpages_cms_page_publish', $paramUrl),
                    'class' => ($page->getIsPublished() == '1')?'kit-cms-advanced':'',
                    'icon' => 'icon/publish.png'
                );
                $pageTree['actionList'][] = array(
                    'id' => '',
                    'label' => 'up',
                    'url'  => $this->generateUrl('kitpages_cms_nav_moveup', $paramUrl),
                    'class' => ($page->getPageType() == 'technical')?'kit-cms-advanced':'',
                    'icon' => 'icon/arrow-up.png'

                );
                $pageTree['actionList'][] = array(
                    'id' => '',
                    'label' => 'down',
                    'url'  => $this->generateUrl('kitpages_cms_nav_movedown', $paramUrl),
                    'class' => ($page->getPageType() == 'technical')?'kit-cms-advanced':'',
                    'icon' => 'icon/arrow-down.png'
                );
                $pageTree['actionList'][] = array(
                    'id' => '',
                    'label' => 'add page technical',
                    'url'  => $this->generateUrl('kitpages_cms_page_create_technical', $paramUrlCreate),
                    'class' => 'kit-cms-advanced'
                );
                $pageTree['actionList'][] = array(
                    'id' => '',
                    'label' => 'add page link',
                    'url'  => $this->generateUrl('kitpages_cms_page_create_link', $paramUrlCreate),
                    'class' => 'kit-cms-advanced'
                );
                $pageTree['actionList'][] = array(
                    'id' => '',
                    'label' => 'delete',
                    'url'  => $this->generateUrl('kitpages_cms_page_delete', $paramUrl),
                    'class' => ($page->getPageType() == 'technical')?'kit-cms-advanced':'',
                    'icon' => 'icon/delete.png',
                    'class' => 'kit-cms-advanced'
                );

            }


            if ($page->getPageType() == 'edito') {
                $pageTree['url'] = $this->generateUrl(
                    'kitpages_cms_page_view_lang',
                    array(
                        'id' => $page->getId(),
                        '_locale' => $page->getLanguage(),
                        'urlTitle' => $page->getUrlTitle()
                    )
                );
            } elseif($page->getPageType() == 'technical') {
                $pageTree['url'] = $this->generateUrl('kitpages_cms_page_edit_technical', $paramUrl);
            } elseif($page->getPageType() == 'link') {
                $pageTree['url'] = $this->generateUrl('kitpages_cms_page_edit_link', $paramUrl);
                //$pageTree['actionList']['link'] = $page->getLinkUrl();
                $pageTree['menuTitle'] .= ' <span class="kit-cms-tree-indicator-link">[ -&gt; '.$this->getPageLink($page).']</span>';
            }
            $pageTree['children'] = $this->treeChildren($page);
            $pageListRenderer[] = $pageTree;
        }
        return $pageListRenderer;
    }
    
    public function getPageLink($page) {
        $url = '';
        if ($page->getPageType() == 'link' ) {
            $url = $page->getLinkUrl();
            if ($page->getIsLinkUrlFirstChild()) {
                $em = $this->getDoctrine()->getManager();
                $pageChildren = $em->getRepository('KitpagesCmsBundle:Page')->children($page, true);
                if (count($pageChildren) > 0 && $pageChildren['0'] instanceof Page) {
                    $url = $this->getPageLink($pageChildren['0']);
                }
            }
        }
        if ($page->getPageType() == 'edito' ) {
            if ($page->getForcedUrl()) {
                $url = $this->getRequest()->getBaseUrl().$page->getForcedUrl();
            } else {
                $url = $this->generateUrl(
                    'kitpages_cms_page_view_lang',
                    array(
                        'id' => $page->getId(),
                        '_locale' => $page->getLanguage(),
                        'urlTitle' => $page->getUrlTitle()
                    )
                );
            }
        }
        return $url;
    }
    
    public function getPagePublishLink($navPublish) {
        $url = '';
        $page = $navPublish->getPage();
        $pagePublish = $page->getPagePublish();
        if ($pagePublish->getPageType() == 'link' ) {
            $url = $navPublish->getLinkUrl();
            if ($navPublish->getIsLinkUrlFirstChild()) {
                $em = $this->getDoctrine()->getManager();
                $navPublishChildren = $em->getRepository('KitpagesCmsBundle:NavPublish')->children($navPublish, true);
                if (count($navPublishChildren) > 0 && $navPublishChildren['0'] instanceof NavPublish) {
                    $url = $this->getPagePublishLink($navPublishChildren['0']);
                }
            }
        }
        if ($pagePublish->getPageType() == 'edito' ) {
            if ($pagePublish->getForcedUrl()) {
                $url = $this->getRequest()->getBaseUrl().$pagePublish->getForcedUrl();
            } else {
                $url = $this->generateUrl(
                    'kitpages_cms_page_view_lang',
                    array(
                        'id' => $page->getId(),
                        '_locale' => $pagePublish->getLanguage(),
                        'urlTitle' => $pagePublish->getUrlTitle()
                    )
                );
            }
        }

        return $url;
    }
}