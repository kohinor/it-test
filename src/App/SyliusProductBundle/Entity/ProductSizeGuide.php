<?php

namespace App\SyliusProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\SyliusProductBundle\Entity\ProductSizeGuideRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sylius_product_size_guide")
 */
class ProductSizeGuide
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
     * @ORM\Column(name="gender", type="string", length=255)
     */
    protected $gender;
    
    /**
    * @ORM\OneToMany(targetEntity="App\SyliusProductBundle\Entity\ProductSizeGuideField", mappedBy="sizeGuide", cascade={"persist", "remove"})
    */
    protected $fields;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    
     /**
     * Set createdAt
     *
     * @ORM\PrePersist
     * @return ProductSizeGuide
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
        $this->fields = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ProductSizeGuide
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
     * Set brand
     *
     * @param string $brand
     * @return ProductSizeGuide
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
     * Set gender
     *
     * @param string $gender
     * @return ProductSizeGuide
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
     * Add fields
     *
     * @param \App\SyliusProductBundle\Entity\ProductSizeGuideField $fields
     * @return ProductSizeGuide
     */
    public function addField(\App\SyliusProductBundle\Entity\ProductSizeGuideField $fields)
    {
        $this->fields[] = $fields;

        return $this;
    }

    /**
     * Remove fields
     *
     * @param \App\SyliusProductBundle\Entity\ProductSizeGuideField $fields
     */
    public function removeField(\App\SyliusProductBundle\Entity\ProductSizeGuideField $fields)
    {
        $this->fields->removeElement($fields);
    }

    /**
     * Get fields
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    public function getValuesAsRows()
    {
        $values = array();
        $rows = array();
        foreach($this->fields as $field) {
            foreach($field->getValues() as $value) {
                $rows[] = $value->getRow();
                $values[$value->getRow()][$field->getId()] = $value->getValue();
            }
        }
        foreach($this->fields as $field) {
            foreach($rows as $row) {
                if (!isset($values[$row][$field->getId()])) {
                    $values[$row][$field->getId()] = '';
                }
            }
        }
        return $values;
    }
}
