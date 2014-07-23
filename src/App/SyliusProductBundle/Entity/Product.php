<?php

namespace App\SyliusProductBundle\Entity;

use Sylius\Component\Core\Model\Product as SyliusCoreProduct;
use Doctrine\Common\Collections\ArrayCollection;
use App\SyliusProductBundle\Entity\ProductVariant;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product")
 */
class Product extends SyliusCoreProduct implements ProductInterface, Translatable
{
    /**
     *
     * @ORM\Column(type="string", name="partner_id", length=64)
     */
    protected $partnerId;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\SyliusProductBundle\Entity\ProductGroup", inversedBy="products")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;
    
    
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    public function getPartnerId()
    {
        return $this->partnerId;
    }

    public function __construct()
    {
        parent::__construct();

        $this->setMasterVariant(new ProductVariant());
        $this->variantSelectionMethod = self::VARIANT_SELECTION_MATCH;
    }

    /**
     * Set group
     *
     * @param \App\SyliusProductBundle\Entity\ProductGroup $group
     * @return Product
     */
    public function setGroup(\App\SyliusProductBundle\Entity\ProductGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \App\SyliusProductBundle\Entity\ProductGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRrp()
    {
        return $this->getMasterVariant()->getRrp();
    }
}
