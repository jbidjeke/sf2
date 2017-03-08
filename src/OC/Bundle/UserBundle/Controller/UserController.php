<?php
namespace OC\Bundle\UserBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function loginAction()
    {
        $response = new JsonResponse();
        // Pour récupérer le service UserManager du bundle
        $userManager = $this->get('fos_user.user_manager');
		if ($_POST){
			// Pour charger un utilisateur
			$user = $userManager->findUserBy(array('email' => $_POST['email']));
			if ($user == null){
				$response->setData("Compte inexistant!");
				return $response;
			}
			$encoder_service = $this->get('security.encoder_factory');
			$encoder = $encoder_service->getEncoder($user);

			if ($encoder->isPasswordValid($user->getPassword(), $_POST['password'], $user->getSalt())) {
				$response->setData(array('id'=>$user->getId(), 'name'=>$user->getName(), 'email'=>$user->getEmail() ));		
			    return $response;
			}
		}
		$response->setData("echec de connexion");
		return $response;

    }

    public function createAction()
    {
		 $response = new JsonResponse();
         $manipulator = $this->container->get('fos_user.util.user_manipulator');
         $manipulator->create("flore", "flore", "flore@yahoo.fr", 1, "");
		 return $response;
    }

    /**
     * Pour l'obtention d'un nouveau mot de passe
     *
     */
    public function passwordAction()
    {
        $response = new JsonResponse();
        // Pour récupérer le service UserManager du bundle
        $userManager = $this->get('fos_user.user_manager');
        // Pour charger un utilisateur
        $user = $userManager->findUserBy(array('email' => $_GET['email']));
        if (null != $user ){
           $pass = $this->randomPassword();
           // Pour modifier un utilisateur
           $user->setPassword($pass);
           $userManager->updateUser($user); // Pas besoin de faire un flush avec l'EntityManager, cette méthode le fait toute seule !
           $response->setData("Un nouveau mot de passe a bien Ã©tÃ© envoyÃ© dans votre messagerie :".$pass);
        }else
           $response->setData("Cet email est inexistant.");
        return $response;

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

}