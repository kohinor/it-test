<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\SyliusCoreBundle\Cart;

use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Resolver\ItemResolvingException;
use Sylius\Bundle\CoreBundle\Cart\ItemResolver as Base;

/**
 * Item resolver for cart bundle.
 * Returns proper item objects for cart add and remove actions.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemResolver extends Base
{
    

    /**
     * {@inheritdoc}
     */
    public function resolve(CartItemInterface $item, $data)
    {
        $id = $this->resolveItemIdentifier($data);

        if (!$product = $this->productRepository->find($id)) {
            throw new ItemResolvingException('Requested product was not found.');
        }

        if ($this->restrictedZoneChecker->isRestricted($product)) {
            throw new ItemResolvingException('Selected item is not available in your country.');
        }

        // We use forms to easily set the quantity and pick variant but you can do here whatever is required to create the item.
        $form = $this->formFactory->create('sylius_cart_item', $item, array('product' => $product));

        $form->submit($data);

        // If our product has no variants, we simply set the master variant of it.
        if (!$product->hasVariants()) {
            $item->setVariant($product->getMasterVariant());
        }

        $variant = $item->getVariant();

        // If all is ok with form, quantity and other stuff, simply return the item.
        if (!$form->isValid() || null === $variant) {
            throw new ItemResolvingException('Please select the size.');
        }

        $cart = $this->cartProvider->getCart();
        $quantity = $item->getQuantity();

        $context = array('quantity' => $quantity);

        if (null !== $user = $cart->getUser()) {
            $context['groups'] = $user->getGroups()->toArray();
        }

        $item->setUnitPrice($this->priceCalculator->calculate($variant, $context));

        foreach ($cart->getItems() as $cartItem) {
            if ($cartItem->equals($item)) {
                $quantity += $cartItem->getQuantity();
                break;
            }
        }

        if (!$this->availabilityChecker->isStockSufficient($variant, $quantity)) {
            throw new ItemResolvingException('Selected item is out of stock.');
        }

        return $item;
    }

}
