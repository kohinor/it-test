<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\SyliusLocaleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\LocaleBundle\Controller\LocaleController as SyliusLocaleController;

class LocaleController extends SyliusLocaleController
{
    public function changeAction(Request $request, $locale)
    {
        $this->getLocaleContext()->setLocale($locale);
        $referer = $request->headers->get('referer') ? $request->headers->get('referer') : '/en';
        $referer = str_replace('/en/', '/'.$locale.'/', $referer);
        $referer = str_replace('/it/', '/'.$locale.'/', $referer);
        $referer = str_replace('/de/', '/'.$locale.'/', $referer);
        $referer = str_replace('/fr/', '/'.$locale.'/', $referer);
        return $this->redirect($referer);
    }

}
