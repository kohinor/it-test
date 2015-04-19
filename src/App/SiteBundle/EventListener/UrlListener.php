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
        if ( !strstr($requestUri, '/it/') && 
             !strstr($requestUri, '/fr/') && 
             !strstr($requestUri, '/en/') && 
             !strstr($requestUri, '/de/') && 
             !strstr($requestUri, 'fragment') && 
             !strstr($requestUri, 'login') && 
             !strstr($requestUri, 'logout')) {
            if (strstr($request->getHost(), '.it')) {
                $response = new RedirectResponse('https://'.str_replace('.it', '.it/it', $request->getHost()).$requestUri);
                $event->setResponse($response);
            } elseif (strstr($request->getHost(), '.fr')){
                $response = new RedirectResponse('https://'.str_replace('.fr', '.fr/fr', $request->getHost()).$requestUri);
                $event->setResponse($response);
            } elseif (strstr( $request->getHost(), '.de')){
                $response = new RedirectResponse('https://'.str_replace('.de', '.de/de', $request->getHost()).$requestUri);
                $event->setResponse($response);
            } elseif (strstr( $request->getHost(), '.local')){
                $response = new RedirectResponse('https://'.str_replace('.local', '.local/en', $request->getHost()).$requestUri);
                $event->setResponse($response);
            } elseif (strstr( $request->getHost(), '.ch')){
                $response = new RedirectResponse('https://'.str_replace('.ch', '.ch/en', $request->getHost()).$requestUri);
                $event->setResponse($response);
            }if (strstr($request->getBaseUrl(), 'italy')) {
                $response = new RedirectResponse('https://'.str_replace('italy', 'italy/it', $request->getHost().$requestUri));
                $event->setResponse($response);
            } elseif (strstr($request->getBaseUrl(), 'france')){
                $response = new RedirectResponse('https://'.str_replace('france', 'france/fr', $request->getHost().$requestUri));
                $event->setResponse($response);
            } elseif (strstr( $request->getBaseUrl(), 'germany')){
                $response = new RedirectResponse('https://'.str_replace('germany', '.germany/de', $request->getHost().$requestUri));
                $event->setResponse($response);
            } elseif (strstr( $request->getBaseUrl(), 'swiss')){
                $response = new RedirectResponse('https://'.str_replace('swiss', 'swiss/en', $request->getHost().$requestUri));
                $event->setResponse($response);
            }
        }
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 30)),
        );
    }
}
