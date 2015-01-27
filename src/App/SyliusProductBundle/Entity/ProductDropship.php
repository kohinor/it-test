<?php

namespace App\SyliusProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\SyliusProductBundle\Entity\ProductDropshipRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sylius_product_dropship")
 */
class ProductDropship
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    protected $productType;
    
    /**
     * @ORM\Column(name="partner_product_id", type="string", length=255, nullable=true)
     */
    protected $partnerProductId;
    
    /**
     * @ORM\Column(name="brand", type="string", length=255, nullable=true)
     */
    protected $brand;
    
    /**
     * @ORM\Column(name="name_en", type="string", length=255, nullable=true)
     */
    protected $nameEn;
    /**
     * @ORM\Column(name="name_fr", type="string", length=255, nullable=true)
     */
    protected $nameFr;
    
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
     * @ORM\Column(name="description_en", type="text", nullable=true)
     */
    protected $descriptionEn;
    
    /**
     * @ORM\Column(name="description_fr", type="text", nullable=true)
     */
    protected $descriptionFr;
    
    /**
     * @ORM\Column(name="weight", type="string", length=255, nullable=true)
     */
    protected $weight;
    
    /**
     * @ORM\Column(name="picture1", type="string", length=255, nullable=true)
     */
    protected $picture1;
    
    /**
     * @ORM\Column(name="picture2", type="string", length=255, nullable=true)
     */
    protected $picture2;
    
    /**
     * @ORM\Column(name="picture3", type="string", length=255, nullable=true)
     */
    protected $picture3;
    
    /**
     * @ORM\Column(name="firm", type="string", length=255, nullable=true)
     */
    protected $firm;
    
    /**
     * @ORM\Column(name="category", type="string", length=255, nullable=true)
     */
    protected $category;
    
    /**
     * @ORM\Column(name="sub_category", type="string", length=255, nullable=true)
     */
    protected $subCategory;
    
    /**
     * @ORM\Column(name="partner_model_id", type="string", length=255, nullable=true)
     */
    protected $partnerModelId;
    
    /**
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    protected $color;
    
    /**
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     */
    protected $gender;
    // barcode
    
    /**
     * @ORM\Column(name="barcode", type="string", length=255, nullable=true)
     */
    protected $barcode;
    
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
     * Set productType
     *
     * @param string $productType
     * @return DropshipProduct
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * Get productType
     *
     * @return string 
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * Set partnerProductId
     *
     * @param string $partnerProductId
     * @return DropshipProduct
     */
    public function setPartnerProductId($partnerProductId)
    {
        $this->partnerProductId = $partnerProductId;

        return $this;
    }

    /**
     * Get partnerProductId
     *
     * @return string 
     */
    public function getPartnerProductId()
    {
        return $this->partnerProductId;
    }

    /**
     * Set brand
     *
     * @param string $brand
     * @return DropshipProduct
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return string 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set nameEn
     *
     * @param string $nameEn
     * @return DropshipProduct
     */
    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * Get nameEn
     *
     * @return string 
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * Set nameFr
     *
     * @param string $nameFr
     * @return DropshipProduct
     */
    public function setNameFr($nameFr)
    {
        $this->nameFr = $nameFr;

        return $this;
    }

    /**
     * Get nameFr
     *
     * @return string 
     */
    public function getNameFr()
    {
        return $this->nameFr;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return DropshipProduct
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
     * @return DropshipProduct
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
     * @return DropshipProduct
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
     * @return DropshipProduct
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
     * Set descriptionEn
     *
     * @param string $descriptionEn
     * @return DropshipProduct
     */
    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    /**
     * Get descriptionEn
     *
     * @return string 
     */
    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    /**
     * Set descriptionFr
     *
     * @param string $descriptionFr
     * @return DropshipProduct
     */
    public function setDescriptionFr($descriptionFr)
    {
        $this->descriptionFr = $descriptionFr;

        return $this;
    }

    /**
     * Get descriptionFr
     *
     * @return string 
     */
    public function getDescriptionFr()
    {
        return $this->descriptionFr;
    }

    /**
     * Set weight
     *
     * @param string $weight
     * @return DropshipProduct
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set picture1
     *
     * @param string $picture1
     * @return DropshipProduct
     */
    public function setPicture1($picture1)
    {
        $this->picture1 = $picture1;

        return $this;
    }

    /**
     * Get picture1
     *
     * @return string 
     */
    public function getPicture1()
    {
        return $this->picture1;
    }

    /**
     * Set picture2
     *
     * @param string $picture2
     * @return DropshipProduct
     */
    public function setPicture2($picture2)
    {
        $this->picture2 = $picture2;

        return $this;
    }

    /**
     * Get picture2
     *
     * @return string 
     */
    public function getPicture2()
    {
        return $this->picture2;
    }

    /**
     * Set picture3
     *
     * @param string $picture3
     * @return DropshipProduct
     */
    public function setPicture3($picture3)
    {
        $this->picture3 = $picture3;

        return $this;
    }

    /**
     * Get picture3
     *
     * @return string 
     */
    public function getPicture3()
    {
        return $this->picture3;
    }

    /**
     * Set firm
     *
     * @param string $firm
     * @return DropshipProduct
     */
    public function setFirm($firm)
    {
        $this->firm = $firm;

        return $this;
    }

    /**
     * Get firm
     *
     * @return string 
     */
    public function getFirm()
    {
        return $this->firm;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return DropshipProduct
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set subCategory
     *
     * @param string $subCategory
     * @return DropshipProduct
     */
    public function setSubCategory($subCategory)
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    /**
     * Get subCategory
     *
     * @return string 
     */
    public function getSubCategory()
    {
        return $this->subCategory;
    }

    /**
     * Set partnerModelId
     *
     * @param string $partnerModelId
     * @return DropshipProduct
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

    /**
     * Set color
     *
     * @param string $color
     * @return DropshipProduct
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
     * Set gender
     *
     * @param string $gender
     * @return DropshipProduct
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set barcode
     *
     * @param string $barcode
     * @return DropshipProduct
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * Get barcode
     *
     * @return string 
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Set size
     *
     * @param string $size
     * @return DropshipProduct
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
}
