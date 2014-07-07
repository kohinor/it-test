<?php
namespace App\SolrSearchBundle\Service;

use \Solarium\Client;

/**
 * Solr service
 */
class SolrUpdateService
{
    protected $client;
    
    protected $translator;
    
    public function __construct(Client $solr) {
        $this->client = $solr;
    }
    
    /**
     * @param Branch $branch
     */
    public function updateSolrProduct(\Sylius\Component\Core\Model\Product $product)
    {
            $update = $this->client->createUpdate();
            foreach (array('en', 'fr') as $locale) {
                $document = $this->getSolrDocument($update->createDocument(), $product, $locale);
                $update->addDocument($document);
            }
            $this->client->update($update);
    }
    
    private function getImages(\Sylius\Component\Core\Model\Product $product)
    {
        $images =  array();
        foreach ($product->getImages() as $image) {
            if ($image) 
                $images[] = $image->getPath();
        }
        return $images;
    }
    
    private function getAttribute(\Sylius\Component\Core\Model\Product $product, $attributeName)
    {
        $attributes = array();
        foreach ($product->getAttributes() as $attribute) {
            if ($attributeName == $attribute->getPresentation()) {
                $attributes[] = $attribute->getValue();
            }
        }
        return $attributes;
    }
    
    private function getOptions(\Sylius\Component\Core\Model\Product $product, $optionName)
    {
        $options = array();
        if (!$product->getOptions()) return $options;
        foreach ($product->getOptions() as $option) {
            if ($optionName == $option->getPresentation()) {
                foreach ($option->getValues() as $value) {
                    $options[] = $value->getValue();
                }
            }
        }
        return $options;
    }
    
    private function getTaxons(\Sylius\Component\Core\Model\Product $product, $taxonomy, $isRoot = true)
    {
        $taxons = array();
        if (!$product->getTaxons()) return array();
        foreach ($product->getTaxons() as $taxon) {
            if ($taxon->getTaxonomy()->getName() != $taxonomy) continue;
            if ($isRoot != ($taxon->getParent()->getName() == $taxonomy)) {
                if ($taxon->getParent()->getName() == $taxonomy) continue;
                $taxons[] = $taxon->getParent()->getName();
            } else {
                $taxons[] = $taxon->getName();  
            }
        }
        return $taxons;
    }

    /**
     * @param type $doc
     * @param \Sylius\Component\Core\Model\Product $product
     * @param string $locale
     * @return type
     */
    public function getSolrDocument( $doc, \Sylius\Component\Core\Model\Product $product, $locale)
    {
        $name = $product->translate($locale)->getName();
        $doc->locale = $locale;
        $doc->id  = $product->getId().$locale;
        $doc->name = $name ? $name : $product->getName();
        $doc->slug = $product->getSlug();
        $doc->price = $product->getPrice()/100; ;
        $doc->sale_price = 0;
        $doc->image = $product->getImage()->getPath();
        $doc->brand = $this->getAttribute($product, 'Brand');
        $doc->color = $this->getOptions($product, 'Color');
        $doc->size = $this->getOptions($product, 'Size');
        $doc->material = $this->getAttribute($product, 'Material');
        $doc->delivery = $product->getShippingCategory() ? $product->getShippingCategory()->getName() : '';
        $doc->gender = $this->getTaxons($product, 'Gender', true);
        $doc->category1 = $this->getTaxons($product, 'Category', true);
        $doc->category2 = $this->getTaxons($product, 'Category', false); 
        $doc->promotion = $this->getTaxons($product, 'Promotion', true);
        return $doc;
    }
	
    
}