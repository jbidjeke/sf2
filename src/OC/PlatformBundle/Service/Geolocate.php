<?php
// src/OC/PlatformBundle/Service/Geolocate.php
namespace OC\PlatformBundle\Service;

use Doctrine\ORM\Event\LifecycleEventArgs;
use OC\PlatformBundle\Entity\Advert;
/**
* Recuperer les information de la géolocalisation d'une annonce
* @author Bidjeke (jbidjeke@yahoo.fr)
*/
class Geolocate
{

  /**
  * Option du flux
  * @var array
  */
  protected  $opts = array(
      'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" 
      )
    );
   
  /**
  * url de l'api des informations de la géolocalisation
  * @var string
  */
  protected $url = 'http://www.telize.com/geoip';

  /**
  * latitude
  * @var integer
  */
  private $lat = null;

  /**
  * longitude
  * @var integer
  */
  private $lng = null;

  /**
   * Requête et le chargement des informations de la géolocalisation
   * @param  String $url
   * @return void 
   */
  public function __construct($url = null)
  {
     /*if ($url !== null) 
        $this->url = $url;
     // Création d'un flux
     $context = stream_context_create($this->opts); 
     // Accès au fichier HTTP avec les entêtes HTTP indiqués ci-dessus
     $content_json = file_get_contents($this->url, false, $context);
     $content_object = json_decode($content_json); 
     $this->setLat($content_object->latitude)->setLng($content_object->longitude);
     */
  }

  /**
   * Charge les informations de la geolocalisation avant la persistance des donn�es
   * @param  LifecycleEventArgs $args
   * @return void 
   */
  /*public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();
    
    // On veut que les entit�s Advert
    if (!$entity instanceof Advert) {
       return;
    }

    $entityGeolocate = $entity->getGeolocate();
    $entityGeolocate->setLat($this->getLat());
    $entityGeolocate->setLng($this->getLng());
  }*/


  /**
   * @return float
   */
  public function getLat()
  {
    return $this->lat;
  }

  /**
   * @param \Float $lat
   * @return Geolocate
   */
  public function setLat($lat)
  {
    $this->lat = $lat;
    return $this;
  }


  /**
   * @return float
   */
  public function getLng()
  {
    return $this->lng;
  }

  /**
   * @param \Float $lng
   * @return Geolocate
   */
  public function setLng($lng)
  {
    $this->lng = $lng;
    return $this;
  }




}
