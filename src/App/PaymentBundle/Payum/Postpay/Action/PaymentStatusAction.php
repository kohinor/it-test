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
use Payum\Core\Request\StatusRequestInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Request\GetHttpQueryRequest;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractPaymentStateAwareAction;
use Doctrine\Common\Persistence\ObjectManager;

use SM\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    
    public function __construct(
        RepositoryInterface $paymentRepository,
        EventDispatcherInterface $eventDispatcher,
        ObjectManager $objectManager,
        FactoryInterface $factory
    ) {
        parent::__construct($factory);

        $this->paymentRepository = $paymentRepository;
        $this->eventDispatcher   = $eventDispatcher;
        $this->objectManager     = $objectManager;
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

        /** @var $payment PaymentInterface */
        $payment = $request->getModel();
        
        $details = new GetHttpQueryRequest();
        $this->payment->execute($details);
        
        
        if (empty($details['orderID']) || $payment->getOrder()->getNumber() != $details['orderID']) {
            throw new BadRequestHttpException('Order id cannot be guessed');
        }       

        if ($details['amount']*100 != $payment->getOrder()->getTotal()) {
            throw new BadRequestHttpException('Request amount cannot be verified against payment amount.');
        }
        
        if (empty($payment->getDetails())) {
            $request->markNew();
        } elseif (isset($details['STATUS']) && (in_array($details['STATUS'], array(5, 9, 4)))) {
            $request->markSuccess();
        } elseif (isset($details['status']) && (in_array($details['STATUS'], array(2, 93, 52, 92)) )) {   
            $request->markFailed();
        } elseif (isset($details['STATUS']) && (in_array($details['STATUS'], array(41, 51, 91)) )) {   
            $request->markPending();
        } elseif (isset($details['STATUS']) && (in_array($details['STATUS'], array(1)) )) {   
            $request->markCanceled();
        } else {
            $request->markUnknown();
        }
        $payment->setDetails($request->getModel()->getDetails(), $details);

        $nextState = $request->getStatus();
        $this->updatePaymentState($payment, $nextState);

        $this->objectManager->flush();

        //throw new ResponseInteractiveRequest(new Response('OK', 200));  
        
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof StatusRequestInterface &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
