<?php

namespace App\SyliusProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductGroupController extends Controller
{
    public function getGroupsListAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product.group');
        $criteria = $request->query->get('criteria');
        $sorting = $request->query->get('sorting');
        $groups = $repository->createPaginator($criteria, $sorting);
        $groups->setMaxPerPage(50);
        $groups->setCurrentPage($request->get('page', 1));
        return $this->render('AppSyliusProductBundle:ProductGroup:list.html.twig', array('groups' =>  $groups ));
    }
    
    public function viewGroupAction(Request $request, $id)
    {
        $repository = $this->container->get('sylius.repository.product.group');
        $group = $repository->findOneById($id);
        if ($request->getMethod() == 'POST') {
            $productId = $request->request->get('productId');
            $productRepository = $this->container->get('sylius.repository.product');
            $product = $productRepository->findOneById($productId);
            if (!isset($product)) {
                $this->get('session')->getFlashBag()->add('error', 'Product was not found!');
                return $this->redirect($this->generateUrl('app_site_product_group_view', array('id' => $id)));
            }
            $product->setGroup($group);
            $manager = $this->get('sylius.manager.product');
            $manager->persist($product);
            $manager->flush();
            return $this->redirect($this->generateUrl('app_site_product_group_view', array('id' => $id)));
        }
        return $this->render('AppSyliusProductBundle:ProductGroup:view.html.twig', array('group' =>  $group ));
    }
    
    public function deleteGroupAction($id)
    {
        $repository = $this->container->get('sylius.repository.product.group');
        $group = $repository->findOneById($id);
        if (!isset($group)) {
            return $this->redirect($this->generateUrl('app_site_product_group_list'));
        }
        $manager = $this->get('sylius.manager.product');
        foreach ($group->getProducts() as $product) {
            $product->setGroup(null);
            $manager->persist($product);
        }
        $this->getDoctrine()->getManager()->remove($group);
        $manager->flush();
        return $this->redirect($this->generateUrl('app_site_product_group_list'));
    }
    
    public function deleteGroupProductAction($id, $productId)
    {
        $repository = $this->container->get('sylius.repository.product.group');
        $group = $repository->findOneById($id);
        if (!isset($group)) {
            return $this->redirect($this->generateUrl('app_site_product_group_list'));
        }
        $manager = $this->get('sylius.manager.product');
        foreach ($group->getProducts() as $product) {
            if ($product->getId() != $productId) continue;
            $product->setGroup(null);
            $manager->persist($product);
        }
        $manager->flush();
        return $this->redirect($this->generateUrl('app_site_product_group_view', array('id' => $id)));
    }
    
    public function editGroupAction(Request $request, $id = null)
    {
        $repository = $this->container->get('sylius.repository.product.group');
        if ($id) {
            $group = $repository->findOneById($id);
        } else {
            $group = new \App\SyliusProductBundle\Entity\ProductGroup();
        }
        $form = $this->createForm(new \App\SyliusProductBundle\Form\Type\ProductGroupType(), $group);
        if ($request->getMethod() === 'POST' && $request->request->has($form->getname())) {
            $form->bind($request);
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->persist($group);
                $this->getDoctrine()->getManager()->flush($group);
                return $this->redirect($this->generateUrl('app_site_product_group_list', array('_locale' => $request->attributes->get('_locale'))), 301);
            }
        }
        $bindings = array(
            'group' => $group,
            'form' => $form->createView()
        );
        return $this->render('AppSyliusProductBundle:ProductGroup:edit.html.twig', $bindings );
    }
    
}
