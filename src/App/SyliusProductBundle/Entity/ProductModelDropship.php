<?php

namespace App\SyliusProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sylius_product_model_dropship")
 */
class ProductModelDropship
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     
    /**
     * @ORM\ManyToOne(targetEntity="App\SyliusProductBundle\Entity\ProductDropship", inversedBy="models", cascade={"persist"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $productDropship;

    /**
     * sku
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    protected $code;
    /**
     * @ORM\Column(name="quantity", type="string", length=255, nullable=true)
     */
    protected $quantity;
    /**
     * @ORM\Column(name="rrp", type="string", length=255, nullable=true)
     */
    protected $rrp;
    /**
     * @ORM\Column(name="actual_price", type="string", length=255, nullable=true)
     */
    protected $actualPrice;
    
    /**
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    protected $color;
    
    /**
     * @ORM\Column(name="partner_model_id", type="string", length=255, nullable=true)
     */
    protected $partnerModelId;
    
    /**
     * @ORM\Column(name="size", type="string", length=255, nullable=true)
     */
    protected $size;
    
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
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
     * Set code
     *
     * @param string $code
     * @return ProductModelDropship
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     * @return ProductModelDropship
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set rrp
     *
     * @param string $rrp
     * @return ProductModelDropship
     */
    public function setRrp($rrp)
    {
        $this->rrp = $rrp;

        return $this;
    }

    /**
     * Get rrp
     *
     * @return string 
     */
    public function getRrp()
    {
        return $this->rrp;
    }

    /**
     * Set actualPrice
     *
     * @param string $actualPrice
     * @return ProductModelDropship
     */
    public function setActualPrice($actualPrice)
    {
        $this->actualPrice = $actualPrice;

        return $this;
    }

    /**
     * Get actualPrice
     *
     * @return string 
     */
    public function getActualPrice()
    {
        return $this->actualPrice;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return ProductModelDropship
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set size
     *
     * @param string $size
     * @return ProductModelDropship
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set productDropship
     *
     * @param \App\SyliusProductBundle\Entity\ProductDropship $productDropship
     * @return ProductModelDropship
     */
    public function setProductDropship(\App\SyliusProductBundle\Entity\ProductDropship $productDropship = null)
    {
        $this->productDropship = $productDropship;

        return $this;
    }

    /**
     * Get productDropship
     *
     * @return \App\SyliusProductBundle\Entity\ProductDropship 
     */
    public function getProductDropship()
    {
        return $this->productDropship;
    }

    /**
     * Set partnerModelId
     *
     * @param string $partnerModelId
     * @return ProductModelDropship
     */
    public function setPartnerModelId($partnerModelId)
    {
        $this->partnerModelId = $partnerModelId;

        return $this;
    }

    /**
     * Get partnerModelId
     *
     * @return string 
     */
    public function getPartnerModelId()
    {
        return $this->partnerModelId;
    }
}
