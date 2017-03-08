<?php

namespace OC\Bundle\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    protected $name;
    
    /**
     * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Advert", mappedBy="user", cascade={"remove", "persist"})
     */
    protected $adverts;

    /**
    * @param string email
    * @return void 
    */
    public function __construct($email=null)
    {

	    $this->username  = $this->extractOfMail($email)[0];
	    $this->name = $this->username;
	    $this->usernameCanonical  = $this->username;
	    $this->emailCanonical  = $email;
	    $this->enabled  = 1;
	    
	    $this->adverts = new \Doctrine\Common\Collections\ArrayCollection();
	    parent::__construct();

    }

    /**
    * Extract of email
    * @param string email
    * @return array 
    */
    protected function extractOfMail($email){
        return \explode('@', $email);
    }
    
    /**
     * Extract of name
     * @return string
     */
    public function getName(){
        return $this->name;
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
     * Add adverts
     *
     * @param \OC\PlatformBundle\Entity\Advert $adverts
     * @return User
     */
    public function addAdvert(\OC\PlatformBundle\Entity\Advert $adverts)
    {
        $this->adverts[] = $adverts;
    
        return $this;
    }
    
    /**
     * Set adverts
     *
     * @param \Doctrine\Common\Collections\Collection $adverts
     */
    public function setAdvert(\Doctrine\Common\Collections\Collection $adverts)
    {
        $this->adverts[] = $adverts;

    }

    /**
     * Remove adverts
     *
     * @param \OC\PlatformBundle\Entity\Advert $adverts
     */
    public function removeAdvert(\OC\PlatformBundle\Entity\Advert $adverts)
    {
        $this->adverts->removeElement($adverts);
    }

    /**
     * Get adverts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdverts()
    {
        return $this->adverts;
    }
}
