<?php

namespace App\SyliusProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        if (count($lastVisited) > 5 ) {
            $lastVisited = array_slice($lastVisited, 1);
        }
        $session->set('lastVisited', $lastVisited);

        return $this->render('SyliusWebBundle:Frontend/Product:show.html.twig', array('product' =>  $product ));
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
        return $this->render('AppSyliusProductBundle:Product:other.html.twig', array('slug' => $request->attributes->get('slug'),'products' =>  $products ), $response);
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
        return $this->render('AppSyliusProductBundle:Product:last-visited.html.twig', array('products' =>  $products ));
    }
    
    public function getProductStockAction($slug)
    {
        $repository = $this->container->get('sylius.repository.product');
        $product = $repository->findOneBy(array('slug' => $slug)); 
        if (!$product) {
            return JsonResponse::create(array('code' => 404), 404);
        }
        $availableOnDemand = false;
        foreach ($product->getVariants() as $variant) {
            foreach ($variant->getOptions() as $option) {
                if ($option->getValue() == $this->get('request')->request->get('option')) {
                    $availableOnDemand = $variant->isAvailableOnDemand();
                    if ($variant->getOnHand() > 1) {
                        $html = $variant->getOnHand().' '.$this->get('translator')->trans('items in stock');
                    } else if($variant->getOnHand() == 1) {
                        $html = $this->get('translator')->trans('Only one item left');
                }
                    $html = '<span class="text-success">'.$html.'</span>';
                    if ($variant->getOnHand() > 0) {
                        return JsonResponse::create(array('code' => 200, 'html' => $html), 200);
                    }
                }
            }
        }
        $html = '<span class="text-danger">'.$this->get('translator')->trans('The item is sold out').'</span>';
        if ($availableOnDemand) {
            $html .= '<br /><br />'. $this->get('translator')->trans('You still can pre-order this item');
        }
        return JsonResponse::create(array('code' => 200, 'html' => $html), 200);
    }
    
    public function getVariantStockAction($id)
    {
        $repository = $this->container->get('sylius.repository.product_variant');
        $variant = $repository->find($id);
        return JsonResponse::create(array('code' => 200, 'html' => $variant->getOnHand()), 200);
    }
}
