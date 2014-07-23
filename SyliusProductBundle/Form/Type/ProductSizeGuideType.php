<?php

namespace App\SyliusProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Product form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductSizeGuideType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
                'label' => "sylius_product_size_guide_name"
            ))
        ->add('brand', 'entity', array(
                'class' => 'Sylius\Component\Core\Model\Taxon',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('o')
                        ->join('o.taxonomy', 't')
                        ->where('t.name = \'Brand\'')
                        ->andWhere('o.parent IS NOT NULL');
                },
            ))
        ->add('gender', 'choice', array(
                'choices'   => array('men' => 'Men', 'women' => 'Women'),
                'required'  => true,
            ));
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => "App\SyliusProductBundle\Entity\ProductSizeGuide"
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_group';
    }
}
