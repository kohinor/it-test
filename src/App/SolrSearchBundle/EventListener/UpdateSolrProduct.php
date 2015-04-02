<?php
namespace App\SolrSearchBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Solarium\Client;
use App\SolrSearchBundle\Service\SolrUpdateService;

class UpdateSolrProduct
{
    /**
     * @var <type>
     */
    protected $solr = null;

    protected $solrService = null;

    /**
     * @param Solarium_Client $solr
     */
    public function __construct(Client $solr, SolrUpdateService $solrService)
    {
        $this->solr = $solr;
        $this->solrService = $solrService;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof \Sylius\Component\Core\Model\Product)
            $this->solrService->updateSolrProduct($entity);
        if ($entity instanceof \Sylius\Component\Core\Model\ProductVariant)
            $this->solrService->updateSolrProduct($entity->getProduct());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof \Sylius\Component\Core\Model\Product)
            $this->solrService->updateSolrProduct($entity);
        if ($entity instanceof \Sylius\Component\Core\Model\ProductVariant)
            $this->solrService->updateSolrProduct($entity->getProduct());
    }
}