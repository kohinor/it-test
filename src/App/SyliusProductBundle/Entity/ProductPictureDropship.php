<?php

namespace App\SyliusProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sylius_product_picture_dropship")
 */
class ProductPictureDropship
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     
    /**
     * @ORM\ManyToOne(targetEntity="App\SyliusProductBundle\Entity\ProductDropship", inversedBy="pictures", cascade={"persist"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $productDropship;
    
    /**
     * sku
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    protected $path;
   
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
     * Set path
     *
     * @param string $path
     * @return ProductPictureDropship
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
}
