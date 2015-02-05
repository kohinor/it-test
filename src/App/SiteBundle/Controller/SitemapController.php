<?php
namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{ 
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function xmlSitemapAction ()
    {
        $baseUrl = $this->container->getParameter('base.url');
        $client = $this->get('solarium.client');
        $query = $this->get('solr.query.service')->getSolrQuery($client, '*');
        $query->setRows(2000);
        $resultset = $client->select($query);
        $view = $this->renderView('AppSiteBundle:Sitemap:sitemap.html.twig', 
                array('products' => $resultset, 
                      'baseurl' => $baseUrl,
                      'pages' => $this->getTree(),
                      'locale' => $this->get('request')->getLocale()));
        $response = new Response($view, 200);
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
    
    public function sitemapAction ()
    {
        $params = array('pages' => $this->getTree(),
                      'locale' => $this->get('request')->getLocale());
        return $this->render('AppSiteBundle:Sitemap:sitemap_site.html.twig', $params);
    }
       
    /**
     * @param int $pageParent
     * @return array
     */
    public function getTree($pageParent = null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        if (is_null($pageParent)) {
            $pageList = $em->getRepository('KitpagesCmsBundle:Page')->getRootNodes();
        } else {
            $pageList = $em->getRepository('KitpagesCmsBundle:Page')->children($pageParent, true);
        }
        $pageListRenderer = array();
        foreach($pageList as $page) {           
            if (!$em->getRepository('KitpagesCmsBundle:PagePublish')->findByPage($page)) continue;
            if ($page->getPageType() == 'edito') {
            $pageTree['url'] = $page->getForcedUrl()?$page->getForcedUrl():$this->generateUrl(
                    'kitpages_cms_page_view_lang',
                    array(
                        'id' => $page->getId(),
                        'lang' => $page->getLanguage(),
                        'urlTitle' => $page->getUrlTitle()
                    )
                );
            $pageTree['publishedAt'] = $page->getPublishedAt();
            $pageTree['menuTitle'] = $page->getMenuTitle();
            $pageListRenderer[] = $pageTree;
            }        
            $pageListRenderer = array_merge($pageListRenderer,$this->getTree($page));
        }
        return $pageListRenderer;
    }
}