<?php

namespace App\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;

class SecurityController extends BaseController
{
    public function loginAction()
    {
        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $request = $this->container->get('request');
        $targetPath = $request->query->get('_target_path');
        $this->container->get('session')->set('_security.'.$providerKey.'.target_path', $targetPath);
    
        $response = parent::loginAction($request);
        return $response;
    }
}
