<?php
namespace App\SolrSearchBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\SolrSearchBundle\Entity\FacetLogRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="solr_facet_log")
 */
class FacetLog {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="facet", type="string", length=255)
     */
    private $facet;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\SolrSearchBundle\Entity\SearchLog", inversedBy="facets", cascade={"persist"})
     * @ORM\JoinColumn(name="search_id", referencedColumnName="id")
     */
    private $search;

    /**
     * @ORM\Column(name="field", type="string", length=255)
     */
    private $field;

    
    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     * @return FacetLog
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
     * Set facet
     *
     * @param string $facet
     * @return FacetLog
     */
    public function setFacet($facet)
    {
        $this->facet = $facet;

        return $this;
    }

    /**
     * Get facet
     *
     * @return string 
     */
    public function getFacet()
    {
        return $this->facet;
    }

    /**
     * Set field
     *
     * @param string $field
     * @return FacetLog
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return string 
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set search
     *
     * @param \App\SolrSearchBundle\Entity\SearchLog $search
     * @return FacetLog
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
