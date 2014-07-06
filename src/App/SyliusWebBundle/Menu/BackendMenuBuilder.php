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

        $child->addChild('translations', array(
            'route' => 'lexik_translation_grid',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-large'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.translations', $section)));
        $child->addChild('Pages', array(
            'route' => 'kitpages_cms_nav_tree',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.pages', $section)));
    }
}
