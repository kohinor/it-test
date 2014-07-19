<?php

namespace App\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsletterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'text', array(
                'label' => "Subscribe",
                'required' => true,
                'attr' => array('placeholder'=> "Provide your email")
            ))
        ->add('men', 'checkbox', array(
            'label' => 'Men',
            'required' => false
            ))
        ->add('women', 'checkbox', array(
            'label' => 'Women',
            'required' => false
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => "App\SiteBundle\Entity\Newsletter"
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'newsletter';
    }
}
