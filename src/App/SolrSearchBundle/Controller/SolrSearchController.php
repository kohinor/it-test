<?php
namespace App\SolrSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;

class SolrSearchController extends Controller
{   
    public function searchNewInAction(Request $request, $gender, $limit = 3)
    {
        $facets['gender'] = $gender;
        $facets['promotion'] = 'new';
                
        $client = $this->get('solarium.client');
        $query = $this->get('solr.query.service')->getSolrQuery($client, '*');
        $this->get('solr.query.service')->setFacets($query, $this->getFacets($facets));
        $query->setRows($limit);
        
        $key =  implode('-', $facets).$limit;
        $cache   = $this->container->get('doctrine_cache.providers.memcached');
        $resultset = $cache->fetch($key);
        if (!$resultset) {
            $resultset = $client->select($query);
            $cache->save($key, $resultset);
        }
        $bindings = array('results' => $resultset, 'locale' => $request->attributes->get('_locale'));
        
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
        
        //$solrRequest = $client->createRequest($query);
        //print $solrRequest->getUri();
        $key = 'facets-'.implode('-', $facets);
        $cache   = $this->container->get('doctrine_cache.providers.memcached');
        $resultset = $cache->fetch($key);
        if (!$resultset) {
            $resultset = $client->select($query);
            $cache->save($key, $resultset);
        }
        
        $facets = $this->getFacetsFromRequest($request);
        if ($resultset->getFacetSet()->getFacet('categories')->count() > 0) {
            $category = $this->getFacetTemplate('categories', $resultset, $facets);
        } else {
            $category = $this->getFacetTemplate('category1', $resultset, $facets);
        }
        $bindings = array(
                'size' => $this->getFacetTemplate('size', $resultset, $facets),
                'color' => $this->getFacetTemplate('color', $resultset, $facets),
                'brand' => $this->getFacetTemplate('brand', $resultset, $facets),
                'material' => $this->getFacetTemplate('material', $resultset, $facets),
                'gender' => $this->getFacetTemplate('gender', $resultset, $facets),
                'delivery' => $this->getFacetTemplate('delivery', $resultset, $facets),
                'promotion' => $this->getFacetTemplate('promotion', $resultset, $facets),
                'category' => $category
                );
        return $bindings;
    }
    
    public function getResultsPaginator(Request $request, $initialFacets)
    {
        $client = $this->get('solarium.client');
        $term  = $request->query->get('term');
        $price = $request->query->get('price') ? explode(';', $request->query->get('price')) : null;
        
        $startPrice = $price ? $price[0] : 0;
        $endPrice = $price ? $price[1] : 10000;
        $page  = $request->query->get('page') ? $request->query->get('page') : 1;
        $query = $this->get('solr.query.service')->getSolrQuery($client, $term, $startPrice*100, $endPrice*100);
        $facets = $this->getFacetsFromRequest($request, $initialFacets);
        $this->get('solr.query.service')->setFacets($query, $facets);
        $keyFacets = array();
        foreach($facets as $facet) {
            $keyFacets[] = $facet->facet;
        }
        $key = 'paginator-'.implode('-', $keyFacets).$term.$page.$startPrice.$endPrice;
        $cache   = $this->container->get('doctrine_cache.providers.memcached');
        $paginator = $cache->fetch($key);
        if (!$paginator) {
            $paginator = $this->get('knp_paginator')->paginate(array($client, $query), $page, 20);
            $cache->save($key, $paginator);
        }
        $solrRequest = $client->createRequest($query);
        // print $solrRequest->getUri();
        if (count($initialFacets) == count($facets)) {
            $type = \App\SolrSearchBundle\Entity\SearchLog::TYPE_MAIN;
        } else {
            $type = \App\SolrSearchBundle\Entity\SearchLog::TYPE_REFINMENT;
        }
        $this->addSearchLog($term, $solrRequest->getUri(), $page, $facets, $paginator, $type);
        return array('pagination' => $paginator, 'facets' => $facets);
        
    }
    
    public function addSearchLog($term, $query, $page, $facets, $resultset, $type, $aggr = true)
    {
        if ($page > 1) {
            $aggr = false;
        }
        $searchLog = new \App\SolrSearchBundle\Entity\SearchLog($this->get('request')->getClientIp(true));
        $searchLog->setAggregate($aggr);
        $searchLog->setQuery($query);
        $searchLog->setTerm($term);
        $user = $this->getUser();
        if ($user && $user instanceof UserInterface 
                && ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') || $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            $searchLog->setUser($user);
        }
        $searchLog->setType($type);
        
        //add facets
        foreach ($facets as $facet) {
            $facetLog = new \App\SolrSearchBundle\Entity\FacetLog();
            $facetLog->setFacet($facet->facet);
            $facetLog->setField($facet->field);
            $facetLog->setSearch($searchLog);
            $searchLog->addFacet($facetLog);   
            $this->getDoctrine()->getManager()->persist($facetLog);
        }
        
        //add productIds
        $productSlugs = array();
        foreach ($resultset as $result) {
            $productSlugs[] = $result['slug'];
            $productSearchLog = new \App\SolrSearchBundle\Entity\ProductSearchLog();
            $productSearchLog->setProductId($result['id']);
            $productSearchLog->setSearch($searchLog);
            $searchLog->addProductId($productSearchLog);
            $this->getDoctrine()->getManager()->persist($productSearchLog);
        }
        
        $searchLog->setResult(json_encode($productSlugs));
        $this->getDoctrine()->getManager()->persist($searchLog);
        $this->getDoctrine()->getManager()->flush();
        return null;
    }

    public function searchPromotionAction(Request $request, $promotion)
    {
        $facets['promotion'] = str_replace('_',' ', $promotion);
       
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }
    
    

    public function searchBrandAction(Request $request, $brand)
    {
        $facets['brand'] = str_replace('_',' ', $brand);
        
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }

    public function searchGenderBrandAction(Request $request, $gender, $brand)
    {
        $facets['gender'] = $gender;
        $facets['brand'] = str_replace('_',' ', $brand);
        
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
        $facets['category1'] = str_replace('_',' ', $category);
        
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }

    public function searchCategoryAction(Request $request, $category)
    {
        $facets['category1'] = str_replace('_',' ', $category);
        
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }
    
    public function searchAllAction(Request $request)
    {
        
        $bindings = $this->getFacetSearchResult($request);
        $bindings = array_merge($this->getResultsPaginator($request, array()), $bindings);   
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }
    
    public function searchGenderPromotionAction(Request $request, $gender, $promotion)
    {
        $facets['gender'] = $gender;
        $facets['promotion'] = str_replace('_',' ', $promotion);
        
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }
    
    public function searchGenderPromotionCategoryAction(Request $request, $gender, $promotion, $category)
    {
        $facets['gender'] = $gender;
        $facets['promotion'] = str_replace('_',' ', $promotion);
        $facets['category1'] = str_replace('_',' ', $category);
 
        $bindings = $this->getFacetSearchResult($request, $facets);
        $bindings = array_merge($this->getResultsPaginator($request, $facets), $bindings);        
        return $this->render('AppSolrSearchBundle:SolrSearch:view.html.twig', $bindings);
    }

    



    /**
     * @param string $idname
     * @param string $term
     * @param int $radius
     * @param array $resultset
     * @param array $facets
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFacetTemplate($idname, $resultset, $facets)
    {
       // die(var_dump($resultset->getFacetSet()->getFacet('categories')));
        $bindings = array(
            'results' => $resultset,
            'facets' => $facets?$facets:array(),
            'idname' => $idname
        );
        return $this->renderView('AppSolrSearchBundle:SolrSearch:facet-results.html.twig',$bindings);
    }
    
}