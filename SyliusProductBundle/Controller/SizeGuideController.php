<?php

namespace App\SyliusProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SizeGuideController extends Controller
{
    public function getSizeGuideAction(Request $request, $brand, $gender)
    {
        $repository = $this->container->get('sylius.repository.product_size_guide');
        $sizeGuide = $repository->findBy(array('brand' => $brand, 'gender' => $gender)); 
        return $this->render('AppSyliusProductBundle:SizeGuide:size-guide.html.twig', array('locale' => $request->attributes->get('_locale'),'size_guides' =>  $sizeGuide));
    }
    
    
    
    
    public function getSizeGuideListAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product_size_guide');
        $criteria = $request->query->get('criteria');
        $sorting = $request->query->get('sorting');
        $sizeGuides = $repository->createPaginator($criteria, $sorting);
        $sizeGuides->setMaxPerPage(50);
        $sizeGuides->setCurrentPage($request->get('page', 1));
        return $this->render('AppSyliusProductBundle:SizeGuide:list.html.twig', array('sizeGuides' =>  $sizeGuides ));
    }
    
    public function deleteSizeGuideAction($id)
    {
        $repository = $this->container->get('sylius.repository.product_size_guide');
        $sizeGuide = $repository->findOneById($id);
        if (!isset($sizeGuide)) {
            return $this->redirect($this->generateUrl('app_site_product_size_guide_list'));
        }
        $this->getDoctrine()->getManager()->remove($sizeGuide);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('app_site_product_size_guide_list'));
    }
        
    
    
    public function viewSizeGuideAction(Request $request, $id)
    {
        $repository = $this->container->get('sylius.repository.product_size_guide');
        $sizeGuide = $repository->findOneById($id);
        if ($request->getMethod() == 'POST') {
            $fields = $request->request->get('field');
            $rows = $request->request->get('row');
            foreach($fields as $key => $fieldName) {
                $fieldExists = false;
                foreach($sizeGuide->getFields() as $field) {
                    if ($field->getId() == $key) {
                        $field->setName($fieldName);
                        $fieldExists = true;
                        foreach($rows[$key] as $row => $valueName) {
                            $valueExists = false;
                            foreach($field->getValues() as $value) {
                                if ($value->getRow() == $row) {
                                    $value->setValue($valueName);
                                    $valueExists = true;
                                }
                            }
                            if (!$valueExists && $valueName != '') {
                                $value = new \App\SyliusProductBundle\Entity\ProductSizeGuideValue();
                                $value->setRow($row);
                                $value->setValue($valueName);
                                $value->setField($field);
                                $field->addValue($value);
                                $this->getDoctrine()->getManager()->persist($value);
                            }
                        }
                    }
                }
                if (!$fieldExists && $fieldName != '') {
                    $field = new \App\SyliusProductBundle\Entity\ProductSizeGuideField();
                    $field->setName($fieldName);
                    $field->setSizeGuide($sizeGuide);
                    $sizeGuide->addField($field);
                    $this->getDoctrine()->getManager()->persist($field);
                }
            }
            $this->getDoctrine()->getManager()->persist($sizeGuide);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('app_site_product_size_guide_view', array('id' => $id)));

        }
        return $this->render('AppSyliusProductBundle:SizeGuide:view.html.twig', array('sizeGuide' =>  $sizeGuide));
    }
    
    
    public function editSizeGuideAction(Request $request, $id = null)
    {
        $repository = $this->container->get('sylius.repository.product_size_guide');
        if ($id) {
            $sizeGuide = $repository->findOneById($id);
        } else {
            $sizeGuide = new \App\SyliusProductBundle\Entity\ProductSizeGuide();
        }
        $form = $this->createForm(new \App\SyliusProductBundle\Form\Type\ProductSizeGuideType(), $sizeGuide);
        if ($request->getMethod() === 'POST' && $request->request->has($form->getname())) {
            $form->bind($request);
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->persist($sizeGuide);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirect($this->generateUrl('app_site_product_size_guide_list', array('_locale' => $request->attributes->get('_locale'))), 301);
            }
        }
        $bindings = array(
            'sizeGuide' => $sizeGuide,
            'form' => $form->createView()
        );
        return $this->render('AppSyliusProductBundle:SizeGuide:edit.html.twig', $bindings );
    }
}
