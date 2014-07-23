<?php

namespace App\SiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\SiteBundle\Entity\SubscriptionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="newsletter")
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;
    
    /**
     * @ORM\Column(name="men", type="boolean")
     */
    protected $men;
    
    
    /**
     * @ORM\Column(name="women", type="boolean")
     */
    protected $women;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    
   
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
     * Set createdAt
     *
     * @ORM\PrePersist
     * @return Subscription
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
     * Set email
     *
     * @param string $email
     * @return Subscription
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set men
     *
     * @param boolean $men
     * @return Subscription
     */
    public function setMen($men)
    {
        $this->men = $men;

        return $this;
    }

    /**
     * Get men
     *
     * @return boolean 
     */
    public function getMen()
    {
        return $this->men;
    }

    /**
     * Set women
     *
     * @param boolean $women
     * @return Subscription
     */
    public function setWomen($women)
    {
        $this->women = $women;

        return $this;
    }

    /**
     * Get women
     *
     * @return boolean 
     */
    public function getWomen()
    {
        return $this->women;
    }
}
