<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Geolocate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Entity\GeolocateRepository")
 */
class Geolocate
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var double
     *
     * @ORM\Column(name="lat", type="float")
     */
    private $lat;

    /**
     * @var double
     *
     * @ORM\Column(name="lng", type="float")
     */
    private $lng;
    

    /**
     *Initialise les données de geolocalisation
     *
     * @param Geolocate
     */
    /*public function __construct($Geolocate = null)
    {
        if ($Geolocate !== null){
           $this->setLat($Geolocate->getLat());
           $this->setLng($Geolocate->getLng());
        }
    }*/

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
     * Set lat
     *
     * @param integer $lat
     * @return Geolocate
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return integer 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param integer $lng
     * @return Geolocate
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return integer 
     */
    public function getLng()
    {
        return $this->lng;
    }
}
