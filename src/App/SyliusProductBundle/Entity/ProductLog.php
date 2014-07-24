<?php
namespace App\SyliusProductBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\SyliusProductBundle\Entity\ProductLogRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sylius_product_log")
 */
class ProductLog {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @ORM\ManyToOne(targetEntity="App\SyliusProductBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     **/
    private $product;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Component\Core\Model\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;

    
    public function __construct($ip, $product)
    {
        $this->ip = $ip;
        $this->product = $product;
    }
    
    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     * @return ProductLog
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
     * Set ip
     *
     * @param string $ip
     * @return ProductLog
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set product
     *
     * @param \App\SyliusProductBundle\Entity\Product $product
     * @return ProductLog
     */
    public function setProduct(\App\SyliusProductBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \App\SyliusProductBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set user
     *
     * @param \Sylius\Component\Core\Model\User $user
     * @return ProductLog
     */
    public function setUser(\Sylius\Component\Core\Model\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Sylius\Component\Core\Model\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
