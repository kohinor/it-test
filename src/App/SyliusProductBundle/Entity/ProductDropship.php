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
    * @ORM\OneToMany(targetEntity="App\SyliusProductBundle\Entity\ProductModelDropship", mappedBy="productDropship", cascade={"persist", "remove"})
    */
    protected $models;
    
    /**
    * @ORM\OneToMany(targetEntity="App\SyliusProductBundle\Entity\ProductPictureDropship", mappedBy="productDropship", cascade={"persist", "remove"})
    */
    protected $pictures;
    
    
    /**
     * @ORM\Column(name="partner_product_id", type="string", length=255, nullable=true)
     */
    protected $partnerProductId;
    
    /**
     * @ORM\Column(name="brand", type="string", length=255, nullable=true)
     */
    protected $brand;
    
    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;
    
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
     * @ORM\Column(name="description_it", type="text", nullable=true)
     */
    protected $descriptionIt;
    
    /**
     * @ORM\Column(name="description_de", type="text", nullable=true)
     */
    protected $descriptionDe;
    
    /**
     * @ORM\Column(name="weight", type="string", length=255, nullable=true)
     */
    protected $weight;
    
    /**
     * @ORM\Column(name="category", type="string", length=255, nullable=true)
     */
    protected $category;
    
    /**
     * @ORM\Column(name="sub_category", type="string", length=255, nullable=true)
     */
    protected $subCategory;
    
    /**
     * @ORM\Column(name="sub_category_fr", type="string", length=255, nullable=true)
     */
    protected $subCategoryFr;
    
    /**
     * @ORM\Column(name="sub_category_it", type="string", length=255, nullable=true)
     */
    protected $subCategoryIt;
    
    /**
     * @ORM\Column(name="sub_category_de", type="string", length=255, nullable=true)
     */
    protected $subCategoryDe;
    
    
    /**
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     */
    protected $gender;
    // barcode
    
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;
    
    /**
     * @ORM\Column(name="currency", type="string", length=255, nullable=true)
     */
    protected $currency;
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
        $this->models = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pictures = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set partnerProductId
     *
     * @param string $partnerProductId
     * @return ProductDropship
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
     * @return ProductDropship
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
     * Set name
     *
     * @param string $name
     * @return ProductDropship
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
     * Set code
     *
     * @param string $code
     * @return ProductDropship
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
     * @return ProductDropship
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
     * @return ProductDropship
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
     * @return ProductDropship
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
     * @return ProductDropship
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
     * @return ProductDropship
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
     * @return ProductDropship
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
     * Set category
     *
     * @param string $category
     * @return ProductDropship
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
     * @return ProductDropship
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
     * Set subCategoryFr
     *
     * @param string $subCategoryFr
     * @return ProductDropship
     */
    public function setSubCategoryFr($subCategoryFr)
    {
        $this->subCategoryFr = $subCategoryFr;

        return $this;
    }

    /**
     * Get subCategoryFr
     *
     * @return string 
     */
    public function getSubCategoryFr()
    {
        return $this->subCategoryFr;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return ProductDropship
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
     * Set currency
     *
     * @param string $currency
     * @return ProductDropship
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Add models
     *
     * @param \App\SyliusProductBundle\Entity\ProductModelDropship $models
     * @return ProductDropship
     */
    public function addModel(\App\SyliusProductBundle\Entity\ProductModelDropship $models)
    {
        $this->models[] = $models;

        return $this;
    }

    /**
     * Remove models
     *
     * @param \App\SyliusProductBundle\Entity\ProductModelDropship $models
     */
    public function removeModel(\App\SyliusProductBundle\Entity\ProductModelDropship $models)
    {
        $this->models->removeElement($models);
    }

    /**
     * Get models
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * Add pictures
     *
     * @param \App\SyliusProductBundle\Entity\ProductPictureDropship $pictures
     * @return ProductDropship
     */
    public function addPicture(\App\SyliusProductBundle\Entity\ProductPictureDropship $pictures)
    {
        $this->pictures[] = $pictures;

        return $this;
    }

    /**
     * Remove pictures
     *
     * @param \App\SyliusProductBundle\Entity\ProductPictureDropship $pictures
     */
    public function removePicture(\App\SyliusProductBundle\Entity\ProductPictureDropship $pictures)
    {
        $this->pictures->removeElement($pictures);
    }

    /**
     * Get pictures
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Set descriptionIt
     *
     * @param string $descriptionIt
     * @return ProductDropship
     */
    public function setDescriptionIt($descriptionIt)
    {
        $this->descriptionIt = $descriptionIt;

        return $this;
    }

    /**
     * Get descriptionIt
     *
     * @return string 
     */
    public function getDescriptionIt()
    {
        return $this->descriptionIt;
    }

    /**
     * Set descriptionDe
     *
     * @param string $descriptionDe
     * @return ProductDropship
     */
    public function setDescriptionDe($descriptionDe)
    {
        $this->descriptionDe = $descriptionDe;

        return $this;
    }

    /**
     * Get descriptionDe
     *
     * @return string 
     */
    public function getDescriptionDe()
    {
        return $this->descriptionDe;
    }

    /**
     * Set subCategoryIt
     *
     * @param string $subCategoryIt
     * @return ProductDropship
     */
    public function setSubCategoryIt($subCategoryIt)
    {
        $this->subCategoryIt = $subCategoryIt;

        return $this;
    }

    /**
     * Get subCategoryIt
     *
     * @return string 
     */
    public function getSubCategoryIt()
    {
        return $this->subCategoryIt;
    }

    /**
     * Set subCategoryDe
     *
     * @param string $subCategoryDe
     * @return ProductDropship
     */
    public function setSubCategoryDe($subCategoryDe)
    {
        $this->subCategoryDe = $subCategoryDe;

        return $this;
    }

    /**
     * Get subCategoryDe
     *
     * @return string 
     */
    public function getSubCategoryDe()
    {
        return $this->subCategoryDe;
    }
}
