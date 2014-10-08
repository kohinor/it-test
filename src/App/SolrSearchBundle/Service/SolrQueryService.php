<?php
namespace App\SolrSearchBundle\Service;

use \Solarium\Client;
use \Solarium\QueryType\Select\Query\Query;

class SolrQueryService
{
    /**
     * @var \Solarium\Client 
     */
    protected $solrClient;

    /**
     * @param \Solarium\Client $solrClient
     */
    public function __construct(Client $solrClient)
    {
        $this->solrClient = $solrClient;
    }
    
    /**
     * @param \Solarium\Client $client
     * @param string $term
     * @param int $startPrice
     * @param int $endPrice
     * @param string $sort
     * @return type
     */
    public function getSolrQuery(Client $client, $term = '*', $startPrice = 0, $endPrice = 1000000)
    {
        $term = str_replace(' ', '', $term);
        $client->getPlugin('postbigrequest');
        $query = $client->createSelect();
        $query->setHandler('browse');
        
        $fq = $query->createFilterQuery('price')->setQuery(sprintf('price:[%s TO %s]', $startPrice, $endPrice));
        $query->addFilterQuery($fq);
        $query->setQuery($term);
        return $query;
    }
    
    /**
     * 
     * @param Solarium\QueryType\Select $query
     * @param array $facets
     * @return \Solarium_Query_Select
     */
    public function setFacets(Query $query, $facets)
    {
        $facetSet = $query->getFacetSet();
        $facetSet->createFacetField('size')->setField('size')->setSort('index')->setLimit(200);
        $facetSet->createFacetField('color')->setField('color')->setSort('index')->setLimit(200);
        $facetSet->createFacetField('brand')->setField('brand')->setSort('index');
        $facetSet->createFacetField('material')->setField('material')->setSort('index');
        $facetSet->createFacetField('gender')->setField('gender')->setSort('index');
        $facetSet->createFacetField('delivery')->setField('delivery')->setSort('index');
        $facetSet->createFacetField('promotion')->setField('promotion')->setSort('index')->setLimit(200);
        $facetSet->createFacetField('category1')->setField('category1')->setSort('index');
        $facetSet->createFacetPivot('categories')->addFields('category1,category2')->setMinCount(0);

        if (!empty($facets)) {
            $groupedFacets = array();
            foreach($facets as $facet){
                if($facet->field == 'category1' || $facet->field == 'category2') {
                    $groupedFacets['categories'][] = $facet->field.':"'.$facet->facet.'"';
                } else {
                    $groupedFacets[$facet->field][] = $facet->field.':"'.$facet->facet.'"';
                }
            }
            foreach ($groupedFacets as $field => $facets) {;
                    $fq = $query->createFilterQuery($field)->setQuery(implode(' OR ', $facets));
                    $query->addFilterQuery($fq);
                }         
        }
        return $query;
    }
}