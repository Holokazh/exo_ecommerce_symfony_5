<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')) {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if ($user) {
                // Enregistrement de la demande de reset password en base de données
                $reset_password = new ResetPassword;
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                // Envoyer un email à l'utilisateur avec un lien lui permettant de mettre à jour son mot de passe


                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour " . $user->getFirstname() . "<br/>";
                $content .= "Vous avez demandé à réinitialiser votre mot de passe sur la boutique Klevor.<br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='" . $url . "'>mettre à jour votre mot de passe</a>.";

                $mail = new Mail;
                $mail->send($user->getEmail(), $user->getFullname(), 'Klevor - Réinitialiser votre mot de passe', $content);

                $this->addFlash('notice', 'Un lien vous a été envoyé par mail afin de réinitialiser votre mot de passe.');
            } else {
                $this->addFlash('notice', 'Aucun compte n\'a été trouvé avec cette adresse email.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'update_password')]
    public function update(Request $request, $token, UserPasswordHasherInterface $passwordHasher)
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        // Vérifier si le createdAt = now - 1h
        $now = new DateTimeImmutable();
        if ($now > $reset_password->getCreatedAt()->modify('+ 1 hour')) {
            $this->addFlash('notice', 'Votre demande de mot de passe a expirée. Merci de la renouveler.');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_psw = $form->get('new_password')->getData();

            $password = $passwordHasher->hashPassword($reset_password->getUser(), $new_psw);
            $reset_password->getUser()->setPassword($password);

            $this->entityManager->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');
        }


        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
