<?php

namespace App\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends Controller
{
    public function registerAction(Request $request)
    {
        if (true === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            return new RedirectResponse('/', 301);
        }
        return parent::registerAction($request);
    }
}
