<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/nous-contacter', name: 'contact')]
    public function index(Request $request)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('notice', 'Merci de nous avoir contacté, notre équipe va vous répondre dans les meilleurs délais.');

            $information = $form->getData();

            $content = "Bonjour Administrateur ! Vous venez de recevoir un message de la part de " . $information['prenom'] . " " . $information['nom'] . ".<br/>";
            $content .= "Voici son adresse mail : " . $information['email'] . "<br/>";
            $content .= "Voici son message : " . $information['content'];

            $mail = new Mail;
            $mail->send('contact@virgiletomadon.fr', 'Klevor', 'Klevor - Message depuis la page contact', $content);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
