<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, \Swift_Mailer $mailer): Response
    {

        $formContact = $this->createForm(ContactType::class);
        
        $formContact->handleRequest($request);

        if($formContact->isSubmitted() && $formContact->isValid()){

            $contact = $formContact->getData();

            $message = (new \Swift_Message('Nouveau message'))
                        ->setFrom($contact['email'])
                        ->setTo('said.boufi@gmail.com')
                        ->setBody(
                            $this->renderView(
                                'email/contact.html.twig', compact('contact')
                            ),
                            'text/html'
                        );

            $mailer->send($message);
            
            $this->addFlash('message', 'Message envoyé avec succès');

        }

        return $this->render('contact/index.html.twig', [
            'formContact' => $formContact->createView()
        ]);
    }
}
