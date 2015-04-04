<?php
namespace App\SiteBundle\EventListener;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
/**
 * Used to set the right locale on the request.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var LocaleContextInterface
     */
    protected $localeContext;
    /**
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }
    /**
     * Set the right locale via context.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
           if (strstr($request->getHost(), '.it')) {
                $default = 'it';
            } elseif (strstr($request->getHost(), '.fr')){
                $default = 'fr';
            }
            elseif (strstr( $request->getHost(), '.de')){
                $default = 'de';
            } else {
                $default = 'en';
            }
            $request->setLocale($default);
            $this->localeContext->setLocale($default);
        }
    }
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 30)),
        );
    }
}