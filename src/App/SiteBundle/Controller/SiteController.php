<?php


namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiteController extends Controller
{
    public function brandListAction()
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
        $taxonomy = $repository->findOneBy(array('name' => 'Brand'));
       
        $taxonRepository = $this->container->get('sylius.repository.taxon');
        return $this->render('AppSiteBundle:Site:brands.html.twig', array('brands' =>  $taxonRepository->getTaxonsAsList($taxonomy)));
    }
    
    public function subscriptionAction(Request $request, $url = '/')
    {
        $newsletter = new \App\SiteBundle\Entity\Subscription();
        $form = $this->createForm(new \App\SiteBundle\Form\Type\SubscriptionType(), $newsletter);
        if ($request->getMethod() === 'POST' && $request->request->has($form->getname())) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($_POST['subscribe'] == 'Men') {
                    $newsletter->setMen(true);
                    $newsletter->setWomen(false);
                } elseif ($_POST['subscribe'] == 'Women') {
                    $newsletter->setWomen(true);
                    $newsletter->setMen(false);
                }
                $this->getDoctrine()->getManager()->persist($newsletter);
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add('success', 'newsletter.subscribed');
            } else {
                $this->get('session')->getFlashBag()->add('error', 'newsletter.error');
            }
            $url = $request->request->get('path') ? $request->request->get('path') : $this->generateUrl('default');
            return $this->redirect($url, 301);
        }
        $bindings = array(
            'form' => $form->createView(),
            'path' => $url
        );
        return $this->render('AppSiteBundle:Subscription:form.html.twig', $bindings);
    }
    
    public function pageAction($key)
    {
        return $this->render('AppSiteBundle:Page:page.html.twig', array('key' => $key));
    }
    
    public function exportAction()
    {
        $container = $this->container;
        $response = new StreamedResponse();
        $response->setCallback(function() use($container){

            $handle = fopen('php://output', 'w+');

            fputcsv($handle, array(
                'ID', 
                'Item Title', 
                'Item Subtitle', 
                'Item Description', 
                'Item Address', 
                'Price', 
                'Sale Price', 
                'Image URL', 
                'Destination URL'),';');
            $results = $container->get('sylius.repository.product')->findBy(array('deletedAt' => null));
            $cacheManager = $container->get('liip_imagine.cache.manager');
            foreach( $results as $row ) {
                $brand = '';
                foreach ($row->getTaxons() as $taxon) {
                if ($taxon->getTaxonomy()->getName() != 'Brand') continue;
                     $brand = $taxon->getName();  
                }
                $line = array(
                    $row->getSlug(),
                    $row->translate($container->get('request')->getLocale())->getName(),
                    $brand,
                    $row->translate($container->get('request')->getLocale())->getDescription(),
                    'Avenue Claude Nobs 14 , c/o Doltec SA , CH -1820 Montreux - Switzerland',
                    $row->getMasterVariant()->getRrp().'EUR',
                    $row->getMasterVariant()->getPrice().'EUR',
                    $cacheManager->getBrowserPath($row->getImage()->getPath(), 'sylius_small'),
                    $container->get('router')->generate('app_site_product', array('slug' => $row->getSlug()), true)
                );
                fputcsv($handle, $line);
            }

            fclose($handle);
        }
        );
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

        return $response;
    }
}
