<?php

namespace App\SyliusProductBundle\Form\Type;

use  Sylius\Bundle\CoreBundle\Form\Type\ProductVariantType as BaseVariantType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Product variant type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantType extends BaseVariantType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('rrp', 'sylius_money', array(
                'label' => 'sylius.form.variant.rrp'
            ));
    }
}
