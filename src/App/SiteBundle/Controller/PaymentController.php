<?php


namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{
    public function postpayTemplateAction()
    {
        $params = array(
            'baseUrl' => $this->container->getParameter('base_url')
        );
        return $this->render('AppSiteBundle:payment:payment.html.twig', $params);
    }
}
