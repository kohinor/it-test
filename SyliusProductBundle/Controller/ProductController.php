<?php

namespace App\SyliusProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;

class ProductController extends Controller
{
    public function getProductAction(Request $request, $slug)
    {
        $repository = $this->container->get('sylius.repository.product');
        $product = $repository->findOneBy(array('slug' => $slug)); 
        if (!$product) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        $session = $this->get('session');
        $lastVisited = $session->get('lastVisited')?$session->get('lastVisited'):array();
        $lastVisited[] = $product->getId();
        $lastVisited = array_unique($lastVisited);
        if (count($lastVisited) > 8 ) {
            $lastVisited = array_slice($lastVisited, 1);
        }
        $session->set('lastVisited', $lastVisited);
        return $this->render('SyliusWebBundle:Frontend/Product:show.html.twig', array('locale' => $request->attributes->get('_locale'),'product' =>  $product ));
    }
    
    public function getProductsByGroupAction(Request $request, $groupId)
    {
        $productRepository = $this->container->get('sylius.repository.product');
        $products = $productRepository->findByGroupId($groupId);
        return $this->render('AppSyliusProductBundle:Product:other.html.twig', array('locale' => $request->attributes->get('_locale'), 'slug' => $request->attributes->get('slug'),'products' =>  $products ));
    }
    
    public function getLastVisitedAction(Request $request)
    {
        $session = $this->get('session');
        $lastVisited = $session->get('lastVisited');
        if (!$lastVisited || !is_array($lastVisited)) {
            return new \Symfony\Component\HttpFoundation\Response();
        }
        $products = array();
        $productRepository = $this->container->get('sylius.repository.product');
        foreach($lastVisited as $productId) {
            $products[] = $productRepository->findOneById($productId);
        }
        return $this->render('AppSyliusProductBundle:Product:last-visited.html.twig', array('locale' => $request->attributes->get('_locale'),'products' =>  $products ));
    }
}
