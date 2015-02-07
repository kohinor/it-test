<?php
namespace App\SiteBundle\Extension;

class TwigExtension extends \Twig_Extension {

    /**
     * @return type
     */
    public function getFilters() {
        return array(
            'friendly_url' => new \Twig_Filter_Method($this, 'friendlyUrl')
        );
    }
    
    
    
    /**
     * @param type $sentence
     * @return type
     */
    public function friendlyUrl($sentence)
    {
        return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', trim($sentence))));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'site_twig_extension';
    }

}
