<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\SyliusCoreBundle\Checkout\Step;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Core\SyliusOrderEvents;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\Form\FormInterface;
use Sylius\Bundle\CoreBundle\Checkout\Step\FinalizeStep as Base;
use Sylius\Component\Core\Model\Payment;

/**
 * Final checkout step.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FinalizeStep extends Base
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_INITIALIZE, $order);
        $paymentMethod = $this->get('sylius.repository.payment.method')->find(7);
        if (!$order->getPayments()->last()) {
            $payment = new Payment();
            $payment->setOrder($order);
            $order->addPayment($payment);
        } else {
            $order->getPayments()->last()->setMethod($paymentMethod);
        }
        
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_PRE_COMPLETE, $order);
        $this->getManager()->persist($order);
        $this->getManager()->flush();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_COMPLETE, $order);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_INITIALIZE, $order);
        
        return $this->renderStep($context, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_INITIALIZE, $order);
        $order->setUser($this->getUser());
        $this->completeOrder($order);

        return $this->complete();
    }

    protected function renderStep(ProcessContextInterface $context, OrderInterface $order)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:finalize.html.twig', array(
            'order'   => $order,
            'context' => $context
        ));
    }
    
   
    protected function createCheckoutPaymentForm(OrderInterface $order)
    {
        return $this->createForm('sylius_checkout_payment', $order);
    }

    /**
     * Mark the order as completed.
     *
     * @param OrderInterface $order
     */
    protected function completeOrder(OrderInterface $order)
    {
        $this->dispatchCheckoutEvent(SyliusOrderEvents::PRE_CREATE, $order);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_PRE_COMPLETE, $order);

        $this->get('sm.factory')->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CREATE, true);

        $manager = $this->get('sylius.manager.order');
        $manager->persist($order);
        $manager->flush();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_COMPLETE, $order);
        $this->dispatchCheckoutEvent(SyliusOrderEvents::POST_CREATE, $order);
    }
}
