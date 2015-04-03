<?php

namespace App\SyliusWebBundle\Menu;

use Knp\Menu\ItemInterface;

/**
 * Main menu builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BackendMenuBuilder extends \Sylius\Bundle\WebBundle\Menu\BackendMenuBuilder
{
    
    protected function addAssortmentMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('assortment', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.assortment', $section)))
        ;

        $child->addChild('taxonomies', array(
            'route' => 'sylius_backend_taxonomy_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-folder-close'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.taxonomies', $section)));

        $child->addChild('products', array(
            'route' => 'sylius_backend_product_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.products', $section)));

        $child->addChild('inventory', array(
            'route' => 'sylius_backend_inventory_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-tasks'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.stockables', $section)));

        $child->addChild('options', array(
            'route' => 'sylius_backend_product_option_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.options', $section)));

        $child->addChild('product_attributes', array(
            'route' => 'sylius_backend_product_attribute_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-list-alt'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.attributes', $section)));

        $child->addChild('product_archetypes', array(
            'route' => 'sylius_backend_product_archetype_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-compressed'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.archetypes', $section)));
        
        $child->addChild('product_groups', array(
            'route' => 'app_site_product_group_list',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.product_groups', $section)));
        $child->addChild('product_size_guide', array(
            'route' => 'app_site_product_size_guide_list',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.product_size_guide', $section)));
    }
    
    /**
     * Add customers menu.
     *
     * @param ItemInterface $menu
     * @param array         $childOptions
     * @param string        $section
     */
    protected function addCustomersMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('customer', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.customer', $section)))
        ;

        $child->addChild('users', array(
            'route' => 'sylius_backend_user_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-user'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.users', $section)));
        $child->addChild('groups', array(
            'route' => 'sylius_backend_group_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-home'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.groups', $section)));
        $child->addChild('subscriptions', array(
            'route' => 'app_site_subscription_list',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-home'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.subscriptions', $section)));
    }

    /**
     * Add content menu.
     *
     * @param ItemInterface $menu
     * @param array         $childOptions
     * @param string        $section
     */
    protected function addContentMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('content', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.content', $section)))
        ;
        
        $child->addChild('blocks', array(
            'route' => 'sylius_backend_block_overview',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-large'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.blocks', $section)));
        $child->addChild('Pages', array(
            'route' => 'sylius_backend_static_content_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.pages', $section)));
        
        $child->addChild('Menus', array(
            'route' => 'sylius_backend_menu_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-list-alt'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.menus', $section)));
        $child->addChild('Slideshow', array(
            'route' => 'sylius_backend_slideshow_block_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-film'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.slideshow', $section)));
        $child->addChild('Routes', array(
            'route' => 'sylius_backend_route_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.routes', $section)));
        
        $child->addChild('translations', array(
            'route' => 'lexik_translation_grid',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-large'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.translations', $section)));
        $child->addChild('Pages Kitpages', array(
            'route' => 'kitpages_cms_nav_tree',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.pages', $section)));
    }
}
