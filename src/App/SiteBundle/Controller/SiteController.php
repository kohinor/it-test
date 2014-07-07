<?php


namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SiteController extends Controller
{

    public function indexAction()
    {
        return $this->redirect('/en/');
    }
    
    public function brandListAction()
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
        $taxonomy = $repository->findOneByName('Brand');
       
        $taxonRepository = $this->container->get('sylius.repository.taxon');
        return $this->render('AppSiteBundle:Site:brands.html.twig', array('brands' =>  $taxonRepository->getTaxonsAsList($taxonomy)));
    }
    
    public function getLatestProductsAction(Request $request, $limit = 5)
    {
        $repository = $this->container->get('sylius.repository.product');
        $products = $repository->findLatest($limit);
        return $this->render('SyliusWebBundle:Frontend/Product:latest.html.twig', array('locale' => $request->attributes->get('_locale'),'products' =>  $products ));
    }
    
    public function getProductAction(Request $request, $slug)
    {
        $repository = $this->container->get('sylius.repository.product');
        $product = $repository->findOneBy(array('slug' => $slug)); 
        return $this->render('SyliusWebBundle:Frontend/Product:show.html.twig', array('locale' => $request->attributes->get('_locale'),'product' =>  $product ));
    }
}
