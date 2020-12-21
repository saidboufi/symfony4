<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface as ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     *@Route("/inscription", name = "security_registration")
     *
     */

     public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){

        $user = new User();

        $registrationForm = $this->createForm(RegistrationType::class, $user);

        $registrationForm->handleRequest($request);

        if($registrationForm->isSubmitted() && $registrationForm->isValid()){

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login');

        }

        return $this->render('security/registration.html.twig', [
            'registrationForm' => $registrationForm->createView()
        ]);

     }

     /**
      * @Route("/connexion", name = "security_login")
      */
     public function login(Request $request){

        $goTo = $request->headers->get('referer');;

        return  $this->render('security/login.html.twig', [
                'goTo' => $goTo
                ]);

     }

     /**
      * @Route("/deconnexion", name="security_logout")
      */

      public function logout(){

        

      }
}
