<?php

namespace App\SyliusProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\SyliusProductBundle\Entity\ProductSizeGuideValueRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sylius_product_size_guide_value")
 */
class ProductSizeGuideValue
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $value;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\SyliusProductBundle\Entity\ProductSizeGuideField", inversedBy="values", cascade={"persist"})
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    protected $field;
    
    
    /**
     * @ORM\Column(name="row", type="integer")
     */
    protected $row;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    
     /**
     * Set createdAt
     *
     * @ORM\PrePersist
     * @return LiveAccessCategory
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
     * Set value
     *
     * @param string $value
     * @return ProductSizeGuideValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set row
     *
     * @param integer $row
     * @return ProductSizeGuideValue
     */
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * Get row
     *
     * @return integer 
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set field
     *
     * @param \App\SyliusProductBundle\Entity\ProductSizeGuideField $field
     * @return ProductSizeGuideValue
     */
    public function setField(\App\SyliusProductBundle\Entity\ProductSizeGuideField $field = null)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return \App\SyliusProductBundle\Entity\ProductSizeGuideField 
     */
    public function getField()
    {
        return $this->field;
    }
}
