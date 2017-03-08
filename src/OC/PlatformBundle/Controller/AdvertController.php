<?php
// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Geolocate;
use OC\Bundle\UserBundle\Entity\User;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Form\AdvertItineraireEditType;
use OC\PlatformBundle\Form\ItineraireType;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertItineraireType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\Pbkdf2PasswordEncoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdvertController extends Controller
{
  public function indexAction(Request $request)
  { 
    $page = $request->get('page', 1);
    if ($page < 1) {
       //$page = 1;
       throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // Ici je fixe le nombre d'annonces par page à  3
    // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
    $nbPerPage = 15;

    $user = $this->getUser();
    
    if (null === $user) {
        return $this->redirect($this->generateUrl('fos_user_security_login')); 
    } else {
        // Ici, $user est une instance de notre classe User
        // On récupère notre objet Paginator
        $id = $user->getId();
        $listAdverts = $this->getDoctrine()
        ->getManager()
        ->getRepository('OCPlatformBundle:Advert')
        ->getAdvertsById($id, $page, $nbPerPage);

    }

    

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts)/$nbPerPage);
    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page $page n'existe pas.");
    }

    // On donne toutes les informations nÃ©cessaires Ã  la vue
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page
    ));
  }

  public function viewAction($id)
  {

    $em = $this->getDoctrine()->getManager();

    // On recupère l'annonce $id
    $advert = $em
      ->getRepository('OCPlatformBundle:Advert')
      ->find($id)
    ;

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On avait déjà  récupéré la liste des candidatures
    $listApplications = $em
      ->getRepository('OCPlatformBundle:Application')
      ->findBy(array('advert' => $advert))
    ;

    // On recupère maintenant la liste des AdvertSkill
    $listAdvertSkills = $em
      ->getRepository('OCPlatformBundle:AdvertSkill')
      ->findBy(array('advert' => $advert))
    ;

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert'           => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills
    ));
  }

  /**
   * Recupère les données dans la base
   * @param string $category
   * @param float $lat
   * @param float $lng
   * @param int $dist
   * @param string $q
   * @return string       // json
   */
  public function ajaxAction($category, $lat, $lng, $dist, $q)
  {


      $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->getAdvertsWithGeolocateAndCategory("Motos", 48, 7, 1000, "");

      $response = new JsonResponse();
      $response->setData($listAdverts);

      return $response;
  }

  /**
  *  Ajouter une annonce
  *
  *  @param Request $request
  *  @return void
  */
  public function addAction(Request $request)
  {
    
    //$categorie = $this->getPost($request);
      $categorie = 0;
      if ($_POST){
           $_POST['oc_platformbundle_advert']  = json_decode($_POST['oc_platformbundle_advert'], true);
           
          /*$_POST['oc_platformbundle_advert']  = array(
           'price'  => $_POST['oc_platformbundle_advert']['price'],
           'title'  => $_POST['oc_platformbundle_advert']['title'],
           'author' => $_POST['oc_platformbundle_advert']['author'],
           'user'   => array(
           'email' => $_POST['oc_platformbundle_advert']['user']['email'],
           ),
           'content'=> $_POST['oc_platformbundle_advert']['content'],
           'categories' => array(
           $_POST['oc_platformbundle_advert']['categories'][0],
           ),
           'itineraire' => array(
           'date'      => $_POST['oc_platformbundle_advert']['itineraire']['date'],
           'departure' => $_POST['oc_platformbundle_advert']['itineraire']['departure'],
           'arrival'   => $_POST['oc_platformbundle_advert']['itineraire']['arrival'],
           'time'      => $_POST['oc_platformbundle_advert']['itineraire']['time'],
           ),
           'save'  => '',
           'published' => 1
          );*/
		  
		  $categorie = $_POST['oc_platformbundle_advert']['categories'][0];

          
          $_FILES['oc_platformbundle_advert'] = array(
              'name' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['name'])),
              'type' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['type'])),
              'tmp_name' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['tmp_name'])),
              'error' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['error'])),
              'size' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['size']))
          );

          $request->initialize(
              $_GET,
              $_POST,
              array(),
              $_COOKIE,
              $_FILES,
              $_SERVER
          );

    }
    $advert = new Advert();
	
    if ($categorie != "15")
       $form = $this->createForm(new AdvertType(), $advert);
    else
       $form = $this->createForm(new AdvertItineraireType(), $advert);
   
    
    if ( $form->handleRequest($request)->isValid() ) {
      $email = $advert->getUser()->getEmail(); 
      $em = $this->getDoctrine()->getManager();
      $user = $em->getRepository('OCUserBundle:User')->findByEmail($email);
      
      if (empty($user)) { // create user
         $pass = $this->randomPassword();
         $username = $this->extractOfMail($email)[0];
         $inactive = 0;
         $superadmin = "";

         $manipulator = $this->container->get('fos_user.util.user_manipulator');
         $manipulator->create($username, $pass, $email, !$inactive, $superadmin);

         $user = $em->getRepository('OCUserBundle:User')->findByEmail($email);

         if( !in_array( $_SERVER['REMOTE_ADDR'], array( '127.0.0.1', '::1' ) ) ){
             $mailer = $this->get('mailer');
             $message = \Swift_Message::newInstance()->setContentType("text/html")
                         ->setSubject("Votre annonce sur http://geoalertinfo.com/")
                         ->setFrom('no-reply@geoalertinfo.com')
                         ->setTo($email)
                         ->setBody( $this->renderView('OCPlatformBundle:Advert:mail.html.twig',array('name'=>$username,'pass'=>$pass, 'email' => $email) ));
             $mailer->send($message);
            }

        }
         
         $advert->setUser($user[0]);

         $geolocate = new Geolocate();
         $geolocate -> setLat($_POST['lat']);
         $geolocate -> setLng($_POST['lng']);
         $advert->setGeolocate($geolocate);
         
         $em->persist($advert);
         //$em->remove($geolocate);
         //$em->remove($advert);

        //try{
          $em->flush(); //Save data advert, geolocate and user
		 //} catch(\Doctrine\ORM\ORMException $e){
		  // flash msg
		  /*$this->get('session')->getFlashBag()->add('error', 'Your custom message');*/
		  // or some shortcut that need to be implemented
		  // $this->addFlash('error', 'Custom message');

		  // error logging - need customization
		  /*$this->get('logger')->error($e->getMessage());*/
		  //$this->get('logger')->error($e->getTraceAsString());
		  // or some shortcut that need to be implemented
		  // $this->logError($e);
          //var_dump($e->getMessage(), $e->getTraceAsString()); 
		  // some redirection e. g. to referer
		  /*return $this->redirect($this->getRequest()->headers->get('referer'));*/
		//} catch(\Exception $e){
		  // other exceptions
		  // flash
		  // logger
		  // redirection
		//}

		 
         //$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrÃ©e.');
         return new Response("Succes de la publication!");

         //return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
    } 
	/*return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView()
    ));*/
    return new Response("Echec de la publication!");
  }

  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    // On recupere l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $categorie = $advert->getCategories()[0]->getId();

    if ($_POST){

        $_POST['oc_platformbundle_advert_edit']  = json_decode($_POST['oc_platformbundle_advert_edit'], true);
        /*$_POST['oc_platformbundle_advert_edit']  = array(
            'price'  => $_POST['oc_platformbundle_advert_edit']['price'],
            'title'  => $_POST['oc_platformbundle_advert_edit']['title'],
            'author' => $_POST['oc_platformbundle_advert_edit']['author'],
            'user'   => array(
                'email' => $_POST['oc_platformbundle_advert_edit']['user']['email'],
            ),
            'content'=> $_POST['oc_platformbundle_advert_edit']['content'],*/
            /*'categories' => array(
                $_POST['oc_platformbundle_advert_edit']['categories'][0],
            ),
            'itineraire' => array(
                'date'      => $_POST['oc_platformbundle_advert_edit']['itineraire']['date'],
                'departure' => $_POST['oc_platformbundle_advert_edit']['itineraire']['departure'],
                'arrival'   => $_POST['oc_platformbundle_advert_edit']['itineraire']['arrival'],
                'time'      => $_POST['oc_platformbundle_advert_edit']['itineraire']['time'],
            ),*/
            /*'save'  => '',
            //'published' => 1
        );*/
        $_FILES['oc_platformbundle_advert_edit'] = array(
            'name' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert_edit']['name'])),
            'type' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert_edit']['type'])),
            'tmp_name' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert_edit']['tmp_name'])),
            'error' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert_edit']['error'])),
            'size' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert_edit']['size']))
        );

        $request->initialize(
            $_GET,
            $_POST,
            array(),
            $_COOKIE,
            $_FILES,
            $_SERVER
        );

    }

    if ($categorie == 15 ){
       $form = $this->createForm(new AdvertItineraireEditType(), $advert);
    }else
       $form = $this->createForm(new AdvertEditType(), $advert);


    if ($form->handleRequest($request)->isValid()) {
      // Inutile de persister ici, Doctrine connait deja  notre annonce
      $em->flush();
      return new Response("Succes de la modification!");
    }
    /*return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
            'form' => $form->createView(),
            'advert' => $advert
    ));*/
    return new Response("Echec de la modification!");
  }

  /**
   * @Security("has_role('ROLE_AUTEUR')")
   */
  public function menuAction($limit)
  {
    $user = $this->getUser();

    if (null === $user)
        $listAdverts = $this->getDoctrine()
          ->getManager()
          ->getRepository('OCPlatformBundle:Advert')
          ->findBy(
            array(),                 // Pas de critere
            array('date' => 'desc'), // On trie par date decroissante
            $limit,                  // On selectionne $limit annonces
            0                        // a partir du premier
            );
    else{
        $id = $user->getId();
        $listAdverts = $this->getDoctrine()
        ->getManager()
        ->getRepository('OCPlatformBundle:Advert')
        ->findBy(
            array('id'=>$id),                 // Pas de critere
            array('date' => 'desc'), // On trie par date decroissante
            $limit,                  // On selectionne $limit annonces
            0                        // a partir du premier
        );
    }

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }


  /**
   * @Security("has_role('ROLE_AUTEUR')")
   */
  public function deleteAction($id, Request $request)
  {

    $em = $this->getDoctrine()->getManager();

    // On rÃ©cupÃ¨re l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On crÃ©e un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protÃ©ger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em->remove($advert);
      $em->flush();

      /*$request->getSession()->getFlashBag()->add('info', "L'annonce a bien Ã©tÃ© supprimÃ©e.");
      return $this->redirect($this->generateUrl('oc_platform_home'));*/
      return new Response("Succes de la suppression!");
    }

    // Si la requÃªte est en GET, on affiche une page de confirmation avant de supprimer
    /*return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
      'advert' => $advert,
      'form'   => $form->createView()
    ));*/
    return new Response("Echec de la suppression!");
  }


  /**
   * Pour l'obtention d'un nouveau mot de passe
   *
  */
  public function passwordAction(Request $request)
  {
      if ($_POST){
          // Pour récupérer le service UserManager du bundle
          $userManager = $this->get('fos_user.user_manager');
          // Pour charger un utilisateur
          $user = $userManager->findUserBy(array('email' => $_POST['email']));
          if (null != $user ){
              $pass = $this->randomPassword();
              // Pour modifier un utilisateur
              $user->setPassword($pass);
              $userManager->updateUser($user); // Pas besoin de faire un flush avec l'EntityManager, cette méthode le fait toute seule !
              $request->getSession()->getFlashBag()->add('notice', "Un nouveau mot de passe a bien Ã©tÃ© envoyÃ© dans votre messagerie :".$pass);
          }else
              $request->getSession()->getFlashBag()->add('notice', "Cet email est inexistant.");
      }

      return $this->render('OCPlatformBundle:Advert:password.html.twig');

  }
  
  
  


  public function testAction()
  {
    $advert = new Advert;

    $advert->setDate(new \Datetime());  // Champ Â« date Â» OK
    $advert->setTitle('abc');           // Champ Â« title Â» incorrect : moins de 10 caractÃ¨res
    //$advert->setContent('blabla');    // Champ Â« content Â» incorrect : on ne le dÃ©finit pas
    $advert->setAuthor('A');            // Champ Â« author Â» incorrect : moins de 2 caractÃ¨res

    // On récupère le service validator
    $validator = $this->get('validator');

    // On declenche la validation sur notre object
    $listErrors = $validator->validate($advert);

    // Si le tableau n'est pas vide, on affiche les erreurs
    if(count($listErrors) > 0) {
      return new Response(print_r($listErrors, true));
    } else {
      return new Response("L'annonce est valide !");
    }
  }

  /**
 * Verifier si la chaine est au format json
 * @param  String $string
 * @return Boolean
 */
  public function isJSON($string){
     return is_string($string) && is_object(json_decode($string)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
  }

  private function randomPassword() {
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $pass = array(); //remember to declare $pass as an array
      $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
      for ($i = 0; $i < 8; $i++) {
          $n = rand(0, $alphaLength);
          $pass[] = $alphabet[$n];
      }
      return implode($pass); //turn the array into a string
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
   * Format Post and Get data
   * @param Request $request
   * @return int
   */
  protected function getPost(&$request){
      $categorie = 0;
      if ($_POST){
          $_POST['oc_platformbundle_advert']  = json_decode($_POST['oc_platformbundle_advert'], true);
          $categorie = $_POST['oc_platformbundle_advert']['categories'][0];
          /* $_POST['oc_platformbundle_advert']  = array(
           'price'  => $_POST['oc_platformbundle_advert']['price'],
           'title'  => $_POST['oc_platformbundle_advert']['title'],
           'author' => $_POST['oc_platformbundle_advert']['author'],
           'user'   => array(
           'email' => $_POST['oc_platformbundle_advert']['user']['email'],
           ),
           'content'=> $_POST['oc_platformbundle_advert']['content'],
           'categories' => array(
           $_POST['oc_platformbundle_advert']['categories'][0],
           ),
           'itineraire' => array(
           'date'      => $_POST['oc_platformbundle_advert']['itineraire']['date'],
           'departure' => $_POST['oc_platformbundle_advert']['itineraire']['departure'],
           'arrival'   => $_POST['oc_platformbundle_advert']['itineraire']['arrival'],
           'time'      => $_POST['oc_platformbundle_advert']['itineraire']['time'],
           ),
           'save'  => '',
           'published' => 1
          );*/


          $_FILES['oc_platformbundle_advert'] = array(
              'name' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['name'])),
              'type' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['type'])),
              'tmp_name' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['tmp_name'])),
              'error' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['error'])),
              'size' =>array('image'=>array('file'=>$_FILES['oc_platformbundle_advert']['size']))
          );

          $request->initialize(
              $_GET,
              $_POST,
              array(),
              $_COOKIE,
              $_FILES,
              $_SERVER
          );


      }

      return $categorie;
  }

}

