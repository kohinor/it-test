<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace App\PaymentBundle\Payum\Postpay\Action;

use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Request\GetHttpRequest;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractPaymentStateAwareAction;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\PayumBundle\Payum\Request\GetStatus;
use SM\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Payum\Core\Bridge\Symfony\Reply\HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class PaymentStatusAction extends AbstractPaymentStateAwareAction
{
    /**
     * @var RepositoryInterface
     */
    protected $paymentRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ObjectManager
     */
    protected $objectManager;
    
    protected $currencyHelper;

    
    public function __construct(
        RepositoryInterface $paymentRepository,
        EventDispatcherInterface $eventDispatcher,
        ObjectManager $objectManager,
        FactoryInterface $factory,
            $currencyHelper
    ) {
        parent::__construct($factory);

        $this->paymentRepository = $paymentRepository;
        $this->eventDispatcher   = $eventDispatcher;
        $this->objectManager     = $objectManager;
        $this->currencyHelper     = $currencyHelper;
    }
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request StatusRequestInterface */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }
        
        $this->payment->execute($httpRequest = new GetHttpRequest());
        $details = $httpRequest->query;

        if (empty($details['orderID'])) {
            throw new BadRequestHttpException('Order id cannot be guessed');
        }

        $payment = $this->paymentRepository->findOneBy(array('id' => $details['orderID']));

        if (null === $payment) {
            throw new BadRequestHttpException('Paymenet cannot be retrieved.');
        }
        $amount = $details['amount']*100;
        if ($amount != $this->currencyHelper->convertAmount($payment->getAmount(), $payment->getCurrency())) {
            
            throw new BadRequestHttpException('Request amount '.$amount.' cannot be verified against payment amount '.$payment->getAmount().'.');
        }

        // Actually update payment details
        $details = array_merge($payment->getDetails(), $details);
        $payment->setDetails($details);

        if (empty($payment->getDetails())) {
            $request->markNew();
        } elseif (isset($details['STATUS']) && (in_array($details['STATUS'], array(5, 9, 4)))) {
            $request->markCaptured();
        } elseif (isset($details['STATUS']) && (in_array($details['STATUS'], array(2, 93, 52, 92)) )) {   
            $request->markFailed();
        } elseif (isset($details['STATUS']) && (in_array($details['STATUS'], array(41, 51, 91)) )) {   
            $request->markPending();
        } elseif (isset($details['STATUS']) && (in_array($details['STATUS'], array(1)) )) {   
            $request->markCanceled();
        } else {
            $request->markUnknown();
        }
        
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
