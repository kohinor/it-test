<?php
namespace App\SolrSearchBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\SolrSearchBundle\Entity\SearchLogRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="solr_search_log")
 */
class SearchLog {

    const TYPE_MAIN = 'Main';
    const TYPE_REFINMENT = 'Refinement';
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="ip", type="string", length=64)
     */
    private $ip;

    /**
     * @ORM\Column(name="query", type="text")
     */
    private $query;

    /**
     * @ORM\Column(name="result", type="text")
     */
    private $result;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\SolrSearchBundle\Entity\ProductSearchLog", mappedBy="search" ,cascade={"persist"})
     */
    private $productIds;
    
    /**
     * @ORM\OneToMany(targetEntity="App\SolrSearchBundle\Entity\FacetLog", mappedBy="search" ,cascade={"persist"})
     */
    private $facets;
    
    /**
     * @ORM\Column(name="aggregate", type="boolean")
     */
    private $aggregate;
    
    /**
     * @ORM\Column(name="term", type="string", length=255, nullable=true)
     */
    private $term;

    /**
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Component\Core\Model\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    
    public function __construct($ip)
    {
        $this->ip = $ip;
        $this->facets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     * @return SearchLog
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
     * Set ip
     *
     * @param string $ip
     * @return SearchLog
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set query
     *
     * @param string $query
     * @return SearchLog
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string 
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set result
     *
     * @param string $result
     * @return SearchLog
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return string 
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set aggregate
     *
     * @param boolean $aggregate
     * @return SearchLog
     */
    public function setAggregate($aggregate)
    {
        $this->aggregate = $aggregate;

        return $this;
    }

    /**
     * Get aggregate
     *
     * @return boolean 
     */
    public function getAggregate()
    {
        return $this->aggregate;
    }

    /**
     * Set term
     *
     * @param string $term
     * @return SearchLog
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get term
     *
     * @return string 
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SearchLog
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add productIds
     *
     * @param \App\SolrSearchBundle\Entity\ProductSearchLog $productIds
     * @return SearchLog
     */
    public function addProductId(\App\SolrSearchBundle\Entity\ProductSearchLog $productIds)
    {
        $this->productIds[] = $productIds;

        return $this;
    }

    /**
     * Remove productIds
     *
     * @param \App\SolrSearchBundle\Entity\ProductSearchLog $productIds
     */
    public function removeProductId(\App\SolrSearchBundle\Entity\ProductSearchLog $productIds)
    {
        $this->productIds->removeElement($productIds);
    }

    /**
     * Get productIds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductIds()
    {
        return $this->productIds;
    }

    /**
     * Add facets
     *
     * @param \App\SolrSearchBundle\Entity\FacetLog $facets
     * @return SearchLog
     */
    public function addFacet(\App\SolrSearchBundle\Entity\FacetLog $facets)
    {
        $this->facets[] = $facets;

        return $this;
    }

    /**
     * Remove facets
     *
     * @param \App\SolrSearchBundle\Entity\FacetLog $facets
     */
    public function removeFacet(\App\SolrSearchBundle\Entity\FacetLog $facets)
    {
        $this->facets->removeElement($facets);
    }

    /**
     * Get facets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * Set user
     *
     * @param \Sylius\Component\Core\Model\User $user
     * @return SearchLog
     */
    public function setUser(\Sylius\Component\Core\Model\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Sylius\Component\Core\Model\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
