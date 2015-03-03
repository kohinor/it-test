<?php


namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SiteController extends Controller
{
    public function brandListAction()
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
        $taxonomy = $repository->find(2);
       
        $taxonRepository = $this->container->get('sylius.repository.taxon');
        return $this->render('AppSiteBundle:Site:brands.html.twig', array('brands' =>  $taxonRepository->getTaxonsAsList($taxonomy)));
    }
    
    public function subscriptionAction(Request $request, $url = '/')
    {
        $newsletter = new \App\SiteBundle\Entity\Subscription();
        $form = $this->createForm(new \App\SiteBundle\Form\Type\SubscriptionType(), $newsletter);
        if ($request->getMethod() === 'POST' && $request->request->has($form->getname())) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($_POST['subscribe'] == 'Men') {
                    $newsletter->setMen(true);
                    $newsletter->setWomen(false);
                } elseif ($_POST['subscribe'] == 'Women') {
                    $newsletter->setWomen(true);
                    $newsletter->setMen(false);
                }
                $this->getDoctrine()->getManager()->persist($newsletter);
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add('success', 'newsletter.subscribed');
            } else {
                $this->get('session')->getFlashBag()->add('error', 'newsletter.error');
            }
            $url = $request->request->get('path') ? $request->request->get('path') : $this->generateUrl('default');
            return $this->redirect($url, 301);
        }
        $bindings = array(
            'form' => $form->createView(),
            'path' => $url
        );
        return $this->render('AppSiteBundle:Subscription:form.html.twig', $bindings);
    }
}
