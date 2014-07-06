<?php
namespace App\SiteBundle\Form\Block;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class Standard extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'title',
            'text',
            array(
                'label' => 'Title',
                'required' => false,
                'attr' => array(
                    "size" => "50"
                )
            )
        );

        $builder->add(
            'subtitle',
            'text',
            array(
                'label' => 'Sub-title',
                'required' => false,
                'attr' => array(
                    "size" => "50"
                )
            )
        );

        $builder->add(
            'mainContent',
            'textarea',
            array(
                'label' => 'Main content',
                'required' => false,
                'attr' => array(
                    "class" => "kit-cms-rte-advanced"
                )
            )
        );
        $builder->add('media_mainImage', 'hidden');

        $builder->add(
            'image_url',
            'text',
            array(
                'required' => false,
                'label' => 'Url of the image',
                'attr' => array(
                    "size" => "50"
                )
            )
        );

        $builder->add(
            'displaySeparator',
            'checkbox',
            array(
                'required' => false,
                'value' => 'YES',
                'label' => 'Display separation bar ?',
                'attr' => array(
                    "class" => "kit-cms-rte-simple"
                )
            )
        );

    }

    public function getName() {
        return 'Standard';
    }

    public function filterList() {
        return array(
            'mainContent' => 'stripTagText',
            'subContent' => 'stripTagText',
        );
    }


}
