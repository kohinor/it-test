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
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_INITIALIZE, $order);
        
        $form = $this->createCheckoutPaymentForm($order);
        
        return $this->renderStep($context, $order, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_INITIALIZE, $order);

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_INITIALIZE, $order);

        $form = $this->createCheckoutPaymentForm($order);

        if ($form->handleRequest($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_PRE_COMPLETE, $order);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_COMPLETE, $order);

            $order->setUser($this->getUser());

            $this->completeOrder($order);
                      
            return $this->complete();
        }
        return $this->renderStep($context, $order, $form);
    }

    protected function renderStep(ProcessContextInterface $context, OrderInterface $order, FormInterface $form)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:finalize.html.twig', array(
            'order'   => $order,
            'form'    => $form->createView(),
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
