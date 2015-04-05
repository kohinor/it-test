<?php

namespace App\SiteBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UrlListener implements EventSubscriberInterface
{
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $requestUri = $request->getRequestUri();
        if (strstr($request, '/en/')) {
            $response = new RedirectResponse(str_replace('/en/', '/', $requestUri));
            $event->setResponse($response);
        }
        if (strstr($request, '/fr/')) {
            $response = new RedirectResponse(str_replace('/fr/', '/', $requestUri));
            $event->setResponse($response);
        }
        if (strstr($request, '/it/')) {
            $response = new RedirectResponse(str_replace('/it/', '/', $requestUri));
            $event->setResponse($response);
        }
        if (strstr($request, '/de/')) {
            $response = new RedirectResponse(str_replace('/de/', '/', $requestUri));
            $event->setResponse($response);
        }
        
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 30)),
        );
    }
}
