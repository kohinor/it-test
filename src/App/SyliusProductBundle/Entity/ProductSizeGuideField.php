<?php

namespace App\SyliusProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\SyliusProductBundle\Entity\ProductSizeGuideFieldRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sylius_product_size_guide_field")
 */
class ProductSizeGuideField
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
    protected $name;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\SyliusProductBundle\Entity\ProductSizeGuide", inversedBy="fields")
     * @ORM\JoinColumn(name="size_guide_id", referencedColumnName="id")
     */
    protected $sizeGuide;
    
    /**
    * @ORM\OneToMany(targetEntity="App\SyliusProductBundle\Entity\ProductSizeGuideValue", mappedBy="field")
    */
    protected $values;
    
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
     * Constructor
     */
    public function __construct()
    {
        $this->values = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ProductSizeGuideField
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sizeGuide
     *
     * @param \App\SyliusProductBundle\Entity\ProductSizeGuide $sizeGuide
     * @return ProductSizeGuideField
     */
    public function setSizeGuide(\App\SyliusProductBundle\Entity\ProductSizeGuide $sizeGuide = null)
    {
        $this->sizeGuide = $sizeGuide;

        return $this;
    }

    /**
     * Get sizeGuide
     *
     * @return \App\SyliusProductBundle\Entity\ProductSizeGuide 
     */
    public function getSizeGuide()
    {
        return $this->sizeGuide;
    }

    /**
     * Add values
     *
     * @param \App\SyliusProductBundle\Entity\ProductSizeGuideValue $values
     * @return ProductSizeGuideField
     */
    public function addValue(\App\SyliusProductBundle\Entity\ProductSizeGuideValue $values)
    {
        $this->values[] = $values;

        return $this;
    }

    /**
     * Remove values
     *
     * @param \App\SyliusProductBundle\Entity\ProductSizeGuideValue $values
     */
    public function removeValue(\App\SyliusProductBundle\Entity\ProductSizeGuideValue $values)
    {
        $this->values->removeElement($values);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getValues()
    {
        return $this->values;
    }
}
