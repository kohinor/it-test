<?php

namespace App\SyliusProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductController extends Controller
{
    public function getProductAction(Request $request, $slug)
    {
        $repository = $this->container->get('sylius.repository.product');
        $product = $repository->findOneBy(array('slug' => $slug)); 
        if (!$product) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        $this->addProductLog($request->getClientIp(true), $product);
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
    
    public function addProductLog($ip, $product)
    {
        $productLog = new \App\SyliusProductBundle\Entity\ProductLog($ip, $product);
        $user = $this->getUser();
        if ($user && $user instanceof UserInterface 
                && ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') || $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            $productLog->setUser($user);
        }
        $this->getDoctrine()->getManager()->persist($productLog);
        $this->getDoctrine()->getManager()->flush();
        return null;  
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
