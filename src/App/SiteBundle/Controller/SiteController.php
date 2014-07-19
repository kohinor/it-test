<?php


namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SiteController extends Controller
{

    public function indexAction()
    {
        return $this->redirect('/en/');
    }
    
    public function brandListAction()
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
        $taxonomy = $repository->findOneByName('Brand');
       
        $taxonRepository = $this->container->get('sylius.repository.taxon');
        return $this->render('AppSiteBundle:Site:brands.html.twig', array('brands' =>  $taxonRepository->getTaxonsAsList($taxonomy)));
    }
    
    public function subscriptionAction(Request $request, $url = '/')
    {
        $newsletter = new \App\SiteBundle\Entity\Newsletter();
        $form = $this->createForm(new \App\SiteBundle\Form\Type\NewsletterType(), $newsletter);
        if ($request->getMethod() === 'POST' && $request->request->has($form->getname())) {
            $form->bind($request);
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->persist($newsletter);
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add('success', 'newsletter.subscribed');
            } else {
                foreach ($form->getErrors() as $error){
                    $this->get('session')->getFlashBag()->add('error', $error->getMessage());
                }
            }
            $url = $request->request->get('path') ? $request->request->get('path') : $this->generateUrl('default');
            return $this->redirect($url, 301);
        }
        $bindings = array(
            'form' => $form->createView(),
            'path' => $url
        );
        return $this->render('AppSiteBundle:Newsletter:form.html.twig', $bindings);
    }
}
