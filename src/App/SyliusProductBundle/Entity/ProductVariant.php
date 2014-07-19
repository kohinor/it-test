<?php

namespace App\SyliusProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ProductVariant as BaseVariant;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
class ProductVariant extends BaseVariant
{
 /**
     *
     * @ORM\Column(type="string", name="barcode", length=64)
     */
    protected $barcode;
    
    /**
     *
     * @ORM\Column(type="integer", name="rrp")
     */
    protected $rrp;
    
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
        return $this;
    }
    
    public function getBarcode()
    {
        return $this->barcode;
    }
    
    public function setRrp($rrp)
    {
        $this->rrp = $rrp;
        return $this;
    }
    
    public function getRrp()
    {
        return $this->rrp;
    }
}
