<?php
namespace App\SiteBundle\EventListener;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
/**
 * Used to set the right locale on the request.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyListener implements EventSubscriberInterface
{
    /**
     * @var CurrencyContextInterface
     */
    protected $currencyContext;
    /**
     * @param CurrencyeContextInterface $currencyContext
     */
    public function __construct(CurrencyContextInterface $currencyContext)
    {
        $this->currencyContext = $currencyContext;
    }
    /**
     * Set the right currency via context.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        
        $request = $event->getRequest();
        if (strstr($request->getHost(), '.ch') || strstr($request->getBaseUrl(), 'swiss')) {
            if ($this->currencyContext->getCurrency() != 'CHF') {
                $this->currencyContext->setCurrency('CHF');
            }
        } else {
            if ($this->currencyContext->getCurrency() != 'EUR') {
                $this->currencyContext->setCurrency('EUR');
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