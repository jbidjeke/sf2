<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use OC\PlatformBundle\Validator\Antiflood;

/**
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Entity\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Advert
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="date", type="datetime")
   * @Assert\DateTime()
   */
  private $date;

  /**
   * @ORM\Column(name="price", type="decimal", scale=2)
   */
  private $price;

  /**
   * @ORM\Column(name="title", type="string", length=255)
   * @Assert\Length(min=2)
   */
  private $title;

  /**
   * @ORM\Column(name="author", type="string", length=255)
   * @Assert\Length(min=2)
   */
  private $author;

  /**
   * @ORM\Column(name="content", type="text")
   * @Assert\NotBlank()
   * @Antiflood()
   */
  private $content;

  /**
   * @ORM\Column(name="published", type="boolean")
   */
  private $published = true;
  
  /**
   * @ORM\Column(name="follow", type="boolean")
   */
  private $follow = false;

  /**
   * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Image", cascade={"persist", "remove"})
   * @ORM\JoinColumn(nullable=true)
   * @Assert\Valid()
   */
  private $image;
  
  /**
   * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Itineraire", cascade={"persist", "remove"})
   * @ORM\JoinColumn(nullable=true)
   */
  private $itineraire;

  /**
   * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Geolocate", cascade={"persist", "remove"})
   * @ORM\JoinColumn(nullable=false)
   */
  private $geolocate;

  /**
   * @ORM\ManyToMany(targetEntity="OC\PlatformBundle\Entity\Category", cascade={"persist"})
   */
  private $categories;

  /**
   * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Application", mappedBy="advert")
   */
  private $applications; // Notez le « s », une annonce est liée à plusieurs candidatures

  
  /**
   * @ORM\ManyToOne(targetEntity="OC\Bundle\UserBundle\Entity\User")
   */
  /**
   * @ORM\ManyToOne(targetEntity="OC\Bundle\UserBundle\Entity\User", inversedBy="adverts", cascade={"remove"})
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   */
  private $user; // une ou plusieurs annonces sont liés à un seul utilisateur

  /**
   * @ORM\Column(name="updated_at", type="datetime", nullable=true)
   */
  private $updatedAt;

  /**
   * @ORM\Column(name="nb_applications", type="integer")
   */
  private $nbApplications = 0;

  /**
   * @Gedmo\Slug(fields={"title"})
   * @ORM\Column(length=128, unique=true)
   */
  private $slug;

  public function __construct()
  {
    $this->date         = new \Datetime();
    $this->categories   = new ArrayCollection();
    $this->applications = new ArrayCollection();
  }

  /**
   * @Assert\Callback
   */
  public function isContentValid(ExecutionContextInterface $context)
  {
    /*$forbiddenWords = array('échec', 'abandon');

    // On vérifie que le contenu ne contient pas l'un des mots
    if (preg_match('#'.implode('|', $forbiddenWords).'#', $this->getContent())) {
      // La règle est violée, on définit l'erreur
      $context
        ->buildViolation('Contenu invalide car il contient un mot interdit.') // message
        ->atPath('content')                                                   // attribut de l'objet qui est violé
        ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
      ;
    }*/
  }

  /**
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param \DateTime $date
   * @return Advert
   */
  public function setDate($date)
  {
    $this->date = $date;
    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getDate()
  {
    return $this->date;
  }

  /**
   * @param string $title
   * @return Advert
   */
  public function setTitle($title)
  {
    $this->title = $title;
    return $this;
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param string $author
   * @return Advert
   */
  public function setAuthor($author)
  {
    $this->author = $author;
    return $this;
  }

  /**
   * @return string
   */
  public function getAuthor()
  {
    return $this->author;
  }

  /**
   * @param string $content
   * @return Advert
   */
  public function setContent($content)
  {
    $this->content = $content;
    return $this;
  }

  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * @param boolean $published
   * @return Advert
   */
  public function setPublished($published)
  {
    $this->published = $published;
    return $this;
  }

  /**
   * @return boolean
   */
  public function getPublished()
  {
    return $this->published;
  }

  /**
   * @param Image $image
   * @return Advert
   */
  public function setImage(Image $image = null)
  {
    $this->image = $image;
    return $this;
  }

  /**
   * @return Image
   */
  public function getImage()
  {
    return $this->image;
  }

  public function addCategory(Category $category)
  {
    $this->categories[] = $category;
    return $this;
  }

  public function removeCategory(Category $category)
  {
    $this->categories->removeElement($category);
  }

  public function getCategories()
  {
    return $this->categories;
  }

  /**
   * @param Application $application
   * @return Advert
   */
  public function addApplication(Application $application)
  {
    $this->applications[] = $application;

    // On lie l'annonce à la candidature
    $application->setAdvert($this);

    return $this;
  }

  /**
   * @param Application $application
   */
  public function removeApplication(Application $application)
  {
    $this->applications->removeElement($application);

    // Et si notre relation était facultative (nullable=true, ce qui n'est pas notre cas ici attention) :
    // $application->setAdvert(null);
  }

  /**
   * @return ArrayCollection
   */
  public function getApplications()
  {
    return $this->applications;
  }

  /**
   * @ORM\PreUpdate
   */
  public function updateDate()
  {
    $this->setUpdatedAt(new \Datetime());
  }

  public function setUpdatedAt(\Datetime $updatedAt)
  {
    $this->updatedAt = $updatedAt;
    return $this;
  }

  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  public function increaseApplication()
  {
    $this->nbApplications++;
  }

  public function decreaseApplication()
  {
    $this->nbApplications--;
  }

    /**
     * Set price
     *
     * @param float $price
     * @return Advert
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set nbApplications
     *
     * @param integer $nbApplications
     * @return Advert
     */
    public function setNbApplications($nbApplications)
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    /**
     * Get nbApplications
     *
     * @return integer 
     */
    public function getNbApplications()
    {
        return $this->nbApplications;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Advert
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set user
     *
     * @param \OC\Bundle\UserBundle\Entity\User $user
     * @return Advert
     */
    public function setUser(\OC\Bundle\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \OC\Bundle\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set geolocate
     *
     * @param \OC\PlatformBundle\Entity\Geolocate $geolocate
     * @return Advert
     */
    public function setGeolocate(\OC\PlatformBundle\Entity\Geolocate $geolocate = null)
    {
        $this->geolocate = $geolocate;

        return $this;
    }

    /**
     * Get geolocate
     *
     * @return \OC\PlatformBundle\Entity\Geolocate 
     */
    public function getGeolocate()
    {
        return $this->geolocate;
    }

    /**
     * Set itineraire
     *
     * @param \OC\PlatformBundle\Entity\Itineraire $itineraire
     * @return Advert
     */
    public function setItineraire(\OC\PlatformBundle\Entity\Itineraire $itineraire = null)
    {
        $this->itineraire = $itineraire;
    
        return $this;
    }

    /**
     * Get itineraire
     *
     * @return \OC\PlatformBundle\Entity\Itineraire 
     */
    public function getItineraire()
    {
        return $this->itineraire;
    }

    /**
     * Set follow
     *
     * @param boolean $follow
     * @return Advert
     */
    public function setFollow($follow)
    {
        $this->follow = $follow;
    
        return $this;
    }

    /**
     * Get follow
     *
     * @return boolean 
     */
    public function getFollow()
    {
        return $this->follow;
    }
}
