<?php
namespace App\KitPagesBundle\Controller;

use Kitpages\CmsBundle\Controller\ToolbarController as BaseController;
use Kitpages\CmsBundle\Entity\Site;
use Symfony\Component\HttpFoundation\Request;

class ToolbarController extends BaseController
{
   public function widgetToolbarExceptionAction() {

        $request = $this->getRequest();
        $router = $this->get('router');
        $em = $this->getDoctrine()->getManager();
        if($this->get('security.context')->isGranted('ROLE_CMD_ADMIN')) {

            try {
                $route = $router->match($request->getPathInfo());
            } catch (\Exception $exc) {
                $route = false;
            }

            if ($this->get('kitpages.cms.controller.context')->getViewMode() == Context::VIEW_MODE_PROD) {
                $editMode = $router->generate('kitpages_cms_admin_view_mode_change', array('viewMode'=> 3, 'kitpages_target' => $request->getPathInfo(), 'context'=> 'edit'));
                if ($route && $route['_controller'] == 'Kitpages\CmsBundle\Controller\PageController::viewAction') {
                    $page = $em->getRepository('KitpagesCmsBundle:Page')->find($route['id']);
                    if ($page != null) {
                        $msg = "This page is not yet published.Please change <a href='".$editMode."' >edit mode</a> to publish";
                    }
                } else {
                    $page = $em->getRepository('KitpagesCmsBundle:Page')->findByForcedUrl($request->getPathInfo());
                    if ($page != null) {
                        $pagePublish = $page->getPagePublish();
                        if ($pagePublish != null) {
                            $msg = "You do not have published the latest changes.URL of the page has not been published.
                                <br />Please change <a href='".$editMode."' >edit mode</a> to publish.";
                        } else {
                            $msg = "This page is not yet published.Please change <a href='".$editMode."' >edit mode</a> to publish.";
                        }
                    }
                }
                $dataRender = array('msg' => $msg);
                return $this->render('KitpagesCmsBundle:Toolbar:toolbar-exception.html.twig', $dataRender);
            } else {
//                $prodMode = $router->generate('kitpages_cms_admin_view_mode_change', array('viewMode'=> 3, 'kitpages_target' => $request->getPathInfo(), 'context'=> 'edit'));
                $pagePublish = $em->getRepository('KitpagesCmsBundle:PagePublish')->findByForcedUrl($request->getPathInfo());
                if ($pagePublish != null) {
                    $page = $pagePublish->getPage();
                    if ($page != null) {
                        $pageUrl = $router->generate('kitpages_cms_page_view_lang', array('id'=> $page->getId(), 'urlTitle' => $page->getUrlTitle(), '_locale' => $page->getLanguage()));
                        $msg = "You do not have published the latest changes.URL of the page has not been published.
                            <a href='".$pageUrl."' >To see your page edition click here</a>";
                    } else {
                        $msg = "This page is not yet published.Please change <a href='".$editMode."' >edit mode</a> to publish";
                    }
                }
                $dataRender = array('msg' => $msg);
                return $this->render('KitpagesCmsBundle:Toolbar:toolbar-exception.html.twig', $dataRender);
            }
        } else {
            return '';
        }
    }
}