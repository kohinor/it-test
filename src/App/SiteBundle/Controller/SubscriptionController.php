<?php


namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController extends Controller
{
    
    public function getSubscriptionsListAction(Request $request)
    {
        $repository = $this->container->get('app.repository.subscription');
        $criteria = $request->query->get('criteria');
        $sorting = $request->query->get('sorting');
        $subscriptions = $repository->createPaginator($criteria, $sorting);
        $subscriptions->setMaxPerPage(50);
        $subscriptions->setCurrentPage($request->get('page', 1));
        return $this->render('AppSiteBundle:Subscription:list.html.twig', array('subscriptions' =>  $subscriptions));
    }
    
    public function deleteSubscriptionAction($id)
    {
        $repository = $this->container->get('app.repository.subscription');
        $subscription = $repository->findOneById($id);
        if (!isset($subscription)) {
            return $this->redirect($this->generateUrl('app_site_subscription_list'));
        }
        
        $this->getDoctrine()->getManager()->remove($subscription);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('app_site_subscription_list'));
    }
}
