<?php
namespace App\SolrSearchBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\SolrSearchBundle\Entity\ProductSearchLogRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="solr_product_log")
 */
class ProductSearchLog {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="product_id", type="string", length=255)
     */
    private $productId;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\SolrSearchBundle\Entity\SearchLog", inversedBy="productIds",cascade={"persist"})
     * @ORM\JoinColumn(name="search_id", referencedColumnName="id")
     */
    private $search;


    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     * @return ProductSearchLog
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdDate;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set productId
     *
     * @param string $productId
     * @return ProductSearchLog
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return string 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set search
     *
     * @param \App\SolrSearchBundle\Entity\SearchLog $search
     * @return ProductSearchLog
     */
    public function setSearch(\App\SolrSearchBundle\Entity\SearchLog $search = null)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get search
     *
     * @return \App\SolrSearchBundle\Entity\SearchLog
     */
    public function getSearch()
    {
        return $this->search;
    }
}
