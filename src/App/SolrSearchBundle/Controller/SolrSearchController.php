<?php
namespace App\SolrSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SolrSearchController extends Controller
{
    
    public function searchNewInAction(Request $request, $gender)
    {
        $facets['gender'] = $gender;
        $facets['promotion'] = 'new';
        
        $client = $this->get('solarium.client');
        $query = $this->get('solr.query.service')->getSolrQuery($client, '*');
        $this->get('solr.query.service')->setFacets($query, $this->getFacets($facets));
        $query->setRows(3);
        $resultset = $client->select($query);        
        $bindings = array('results' => $resultset);
        
        return $this->render('AppSolrSearchBundle:SolrSearch:new-in.html.twig', $bindings);
    }
    
    protected function getFacets($facets)
    {
        $facetCollection = array();
        foreach ($facets as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $fieldValue) {
                    $facet = new \stdClass();
                    $facet->field = $key;
                    $facet->facet = $fieldValue;
                    $facetCollection[] = $facet;
                }
            } else {
                $facet = new \stdClass();
                $facet->field = $key;
                $facet->facet = $value;
                $facetCollection[] = $facet;
            }
        }
        return $facetCollection;
    }
    
    public function getFacetsFromRequest(Request $request, $facets = array())
    {
        $promotion = $request->query->get('promotion');
        if ($promotion) {
            $facets['promotion'] = explode(',', $promotion);
        }
        $color = $request->query->get('color');
        if ($color) {
            $facets['color'] = explode(',', $color);
        }
        $size = $request->query->get('size');
        if ($size) {
            $facets['size'] = explode(',', $size);
        }
        $brand = $request->query->get('brand');
        if ($brand) {
            $facets['brand'] = explode(',', $brand);
        }
        $material = $request->query->get('material');
        if ($material) {
            $facets['material'] = explode(',', $material);
        }
        $gender = $request->query->get('gender');
        if ($gender) {
            $facets['gender'] = explode(',', $gender);
        }
        $delivery = $request->query->get('delivery');
        if ($delivery) {
            $facets['delivery'] = explode(',', $delivery);
        }
        $catOne = $request->query->get('category1');
        if ($catOne) {
            $facets['category1'] = explode(',', $catOne);
        }
        $catTwo = $request->query->get('category2');
        if ($catTwo) {
            $facets['category2'] = explode(',', $catTwo);
        }

        return $this->getFacets($facets);                
    }
    
    public function getFacetSearchResult(Request $request, $facets)
    {
        $term  = $request->query->get('term');
        $client = $this->get('solarium.client');            
        $query = $this->get('solr.query.service')->getSolrQuery($client, $term);
        $facetsCollection = $this->getFacets($facets);
        $this->get('solr.query.service')->setFacets($query, $facetsCollection);
        $resultset = $client->select($query);
        $facets = $this->getFacetsFromRequest($request);
        $bindings = array(
                'size' => $this->getFacetTemplate('size', 'size', $resultset, $facets),
                'color' => $this->getFacetTemplate('color', 'color', $resultset, $facets),
                'brand' => $this->getFacetTemplate('brand', 'brand', $resultset, $facets),
                'material' => $this->getFacetTemplate('material', 'material', $resultset, $facets),
                'gender' => $this->getFacetTemplate('gender', 'gender', $resultset, $facets),
                'delivery' => $this->getFacetTemplate('delivery', 'delivery', $resultset, $facets),
                'promotion' => $this->getFacetTemplate('promotion', 'promotion', $resultset, $facets),
                'category1' => $this->getFacetTemplate('category1', 'category1', $resultset, $facets),
                'category2' => $this->getFacetTemplate('category2', 'category2', $resultset, $facets),
                );
        return $bindings;
    }
    
    public function getResultsPaginator(Request $request, $facets)
    {
        $client = $this->get('solarium.client');
        $term  = $request->query->get('term');
        $startPrice = $request->query->get('fromPrice') ? $request->query->get('fromPrice') : 0;
        $endPrice = $request->query->get('toPrice') ? $request->query->get('toPrice') : 10000;
        $page  = $request->query->get('page') ? $request->query->get('page') : 1;
        $query = $this->get('solr.query.service')->getSolrQuery($client, $term, $startPrice, $endPrice);
        $facets = $this->getFacetsFromRequest($request, $facets);
        $this->get('solr.query.service')->setFacets($query, $facets);
        $paginator = $this->get('knp_paginator')->paginate(array($client, $query), $page, 20);
        //$request = $client->createRequest($query);
        //print (string)$request;
        return array('pagination' => $paginator, 'facets' => $facets);
        
    }

    public function searchPromotionAction(Request $request, $promotion)
    {
        $facets['promotion'] = $promotion;
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }
    
    

    public function searchBrandAction(Request $request, $brand)
    {
        $facets['brand'] = $brand;
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }

    public function searchGenderBrandAction(Request $request, $gender, $brand)
    {
        $facets['gender'] = $gender;
        $facets['brand'] = $brand;
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }

    public function searchGenderAction(Request $request, $gender)
    {
        $facets['gender'] = $gender;
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }

    public function searchGenderCategoryAction(Request $request, $gender, $category)
    {
        $facets['gender'] = $gender;
        $facets['category1'] = $category;
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }

    public function searchCategoryAction(Request $request, $category)
    {
        $facets['category1'] = $category;
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }
    
    public function searchAllAction(Request $request)
    {
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }
    
    public function searchGenderPromotionAction(Request $request, $gender, $promotion)
    {
        $facets['gender'] = $gender;
        $facets['promotion'] = $promotion;
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }
    
    public function searchGenderPromotionCategoryAction(Request $request, $gender, $promotion, $category)
    {
        $facets['gender'] = $gender;
        $facets['promotion'] = $promotion;
        $facets['category1'] = $category;
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }

    



    /**
     * @param string $idname
     * @param string $facetkey
     * @param string $term
     * @param int $radius
     * @param array $resultset
     * @param array $facets
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFacetTemplate($idname, $facetkey, $resultset, $facets)
    {
       // die(var_dump($resultset->getFacetSet()->getFacet('size')));
        $bindings = array(
            'results' => $resultset,
            'facets' => $facets?$facets:array(),
            'idname' => $idname,
            'facetkey' => $facetkey,

        );
        //die(var_dump($bindings));
        return $this->renderView('AppSolrSearchBundle:SolrSearch:facet-results.html.twig',$bindings);
    }
    
}