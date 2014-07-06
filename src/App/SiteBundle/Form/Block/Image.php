<?php
namespace App\SiteBundle\Form\Block;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;

class Image extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            'text',
            array(
                'required' => false,
                'attr' => array(
                    "size" => "50"
                )
            )
        );
       
        $builder->add('media_mainImage', 'hidden');

        $builder->add(
            'block_url',
            'text',
            array(
                'required' => false,
                'label' => 'Url of the block',
                'attr' => array(
                    "size" => "50"
                )
            )
        );

    }

    public function getName() {
        return 'AppSiteBlockNews';
    }

}
