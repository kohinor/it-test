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
use Symfony\Component\Form\FormInterface;
use Sylius\Bundle\CoreBundle\Checkout\Step\AddressingStep as Base;
use Sylius\Component\Core\Model\UserInterface;

/**
 * The addressing step of checkout.
 * User enters the shipping and shipping address.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AddressingStep extends Base
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_INITIALIZE, $order);
        
        $shippingId = $this->get('request')->query->get('id_shipping');
        $billingId = $this->get('request')->query->get('id_billing');
        if ($shippingId) {
            $shippingAddress = $this->get('sylius.repository.address')->find($shippingId);
            if ($shippingAddress) {
                $order->setShippingAddress($shippingAddress);
            }
        }
        if ($billingId) {
            $billingAddress = $this->get('sylius.repository.address')->find($billingId);
            if ($billingAddress) {
                $order->setBillingAddress($billingAddress);
            }
        }
        $form = $this->createCheckoutAddressingForm($order, $this->getUser());

        return $this->renderStep($context, $order, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();

        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_INITIALIZE, $order);
        
        $form = $this->createCheckoutAddressingForm($order, $this->getUser());

        if ($form->handleRequest($request)->isValid()) {            
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_PRE_COMPLETE, $order);
            $this->getManager()->persist($order);
            $this->getManager()->flush();
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_COMPLETE, $order);
            
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_INITIALIZE, $order);
            $shippingCountry = $order->getShippingAddress()->getCountry()->getIsoName();
            if ($shippingCountry == 'CH') {
                $shippingMethod = $this->get('sylius.repository.shipping_method')->find(6);
            } else {
                $shippingMethod = $this->get('sylius.repository.shipping_method')->find(1);
            }            
            foreach ($order->getShipments() as $shipment) {
                $shipment->setMethod($shippingMethod);
            }
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_PRE_COMPLETE, $order);
            
            $this->getManager()->persist($order);
            $this->getManager()->flush();
            
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_COMPLETE, $order);
            
            return $this->complete();
        }

        return $this->renderStep($context, $order, $form);
    }

    protected function renderStep(ProcessContextInterface $context, OrderInterface $order, FormInterface $form)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:addressing.html.twig', array(
            'order'   => $order,
            'form'    => $form->createView(),
            'context' => $context,
            'addresses' => $this->getUser()->getAddresses()
        ));
    }

    protected function createCheckoutAddressingForm(OrderInterface $order, UserInterface $user = null)
    {
        return $this->createForm('sylius_checkout_addressing', $order, array('user' => $user, 'validation_groups' => array('sylius')));
    }
}
