<?php

namespace App\CartBundle\Controller;

use Sylius\Bundle\CartBundle\Event\CartItemEvent;
use Sylius\Bundle\CartBundle\Event\FlashEvent;
use Sylius\Component\Cart\Resolver\ItemResolvingException;
use Sylius\Component\Cart\SyliusCartEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\CartBundle\Controller\CartItemController as Controller;


class CartItemController extends Controller
{
    public function addAction(Request $request)
    {
        $cart = $this->getCurrentCart();
        $emptyItem = $this->createNew();

        $eventDispatcher = $this->getEventDispatcher();
        try {
            $item = $this->getResolver()->resolve($emptyItem, $request);
        } catch (ItemResolvingException $exception) {
            // Write flash message
            $eventDispatcher->dispatch(SyliusCartEvents::ITEM_ADD_ERROR, new FlashEvent($exception->getMessage()));
            
            if ($request->query->get('id')) {
                
                return $this->redirectToItem($request->query->get('id'));
            } else {
                return $this->redirectAfterAdd($request);
            }
        }

        $event = new CartItemEvent($cart, $item);
        $event->isFresh(true);

        // Update models
        $eventDispatcher->dispatch(SyliusCartEvents::ITEM_ADD_INITIALIZE, $event);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));
        $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

        // Write flash message
        $eventDispatcher->dispatch(SyliusCartEvents::ITEM_ADD_COMPLETED, new FlashEvent());

        return $this->redirectAfterAdd($request);
    }
    
    private function redirectAfterAdd(Request $request)
    {
        if ($request->query->has('_redirect_to')) {
            return $this->redirect($request->query->get('_redirect_to'));
        }

        return $this->redirectToCartSummary();
    }
    
    public function redirectToItem($id)
    {
        $repository = $this->container->get('sylius.repository.product');
        $product = $repository->findOneById($id);
        $url = $this->generateUrl('app_site_product', array('slug' => $product->getSlug()));
        return $this->redirect($url);
    }

}
