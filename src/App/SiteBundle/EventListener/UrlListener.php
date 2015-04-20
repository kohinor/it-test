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
        $fullUrl = $request->getHost().$requestUri;

        if (strstr($request->getHost(), '.ch') || $requestUri == '/' || $requestUri == '' ) {
            $response = new RedirectResponse('https://shopitalica.com/swiss/en/');
            $event->setResponse($response);
        } elseif (strstr($request->getHost(), '.fr')) {
            $response = new RedirectResponse('https://shopitalica.com/france/fr/');
            $event->setResponse($response);
        } elseif (strstr($request->getHost(), '.de')) {
            $response = new RedirectResponse('https://shopitalica.com/germany/de/');
            $event->setResponse($response);
        } elseif (strstr($request->getHost(), '.it')) {
            $response = new RedirectResponse('https://shopitalica.com/italy/it/');
            $event->setResponse($response);
        } elseif ( strstr($fullUrl, '.com/en/') ) {
            $response = new RedirectResponse('https://'.str_replace('.com/en/', '.com/swiss/en/', $fullUrl));
            $event->setResponse($response);
        } elseif ( strstr($fullUrl, '.com/it/') ) {
            $response = new RedirectResponse('https://'.str_replace('.com/it/', '.com/italy/it/', $fullUrl));
            $event->setResponse($response);
        } elseif ( strstr($fullUrl, '.com/de/') ) {
            $response = new RedirectResponse('https://'.str_replace('.com/de/', '.com/germany/de/', $fullUrl));
            $event->setResponse($response);
        } elseif ( strstr($fullUrl, '.com/fr/') ) {
            $response = new RedirectResponse('https://'.str_replace('.com/fr/', '.com/france/fr/', $fullUrl));
            $event->setResponse($response);
        } elseif(in_array(substr($requestUri, -3), array('/en', '/de', '/fr', '/it'))) {
            $response = new RedirectResponse('https://'.$request->getHost().$requestUri.'/');
            $event->setResponse($response);
        } elseif ( !strstr($requestUri, '/it/') &&
             !strstr($requestUri, '/fr/') &&
             !strstr($requestUri, '/en/') &&
             !strstr($requestUri, '/de/') &&
             !strstr($requestUri, 'fragment') &&
             !strstr($requestUri, 'login') &&
             !strstr($requestUri, 'logout')) {
            if (strstr($request->getBaseUrl(), 'italy')) {
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
