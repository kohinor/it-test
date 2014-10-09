<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\SyliusCoreBundle\Checkout;

use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutProcessScenario as Base;

/**
 * Sylius checkout process.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CheckoutProcessScenario extends Base
{
    /**
     * {@inheritdoc}
     */
    public function build(ProcessBuilderInterface $builder)
    {
        $cart = $this->getCurrentCart();

        $builder
            ->add('security', 'sylius_checkout_security')
            ->add('addressing', 'sylius_checkout_addressing')
            ->add('finalize', 'sylius_checkout_finalize')
            ->add('purchase', 'sylius_checkout_purchase')
        ;

        $builder
            ->setDisplayRoute('sylius_checkout_display')
            ->setForwardRoute('sylius_checkout_forward')
            ->setRedirect('sylius_homepage')
            ->validate(function () use ($cart) {
                return !$cart->isEmpty();
            })
        ;
    }
}
