<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }

        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email) {
                $notification = "Votre inscription a bien été prise en compte. Vous pouvez vous connecter à votre compte.";
            } else {
                $notification = "L'email que vous avez renseigné existe déjà.";
            }

            $user = $form->getData();

            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $mail = new Mail;
            $content = "Bonjour ".$user->getFirstname()."<br/>Votre inscription a bien été prise en compte. Vous pouvez dès à présent accéder à votre compte.";
            $mail->send($user->getEmail(), $user->getFullname(), 'Bienvenue sur le site Klevor', $content);
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
