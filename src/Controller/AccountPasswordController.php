<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/compte/modifier-mon-mot-de-passe", name="account_changePassword")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_psw = $form->get('old_password')->getData();
            if ($passwordHasher->isPasswordValid($user, $old_psw)) {
                $new_psw = $form->get('new_password')->getData();
                $password = $passwordHasher->hashPassword($user, $new_psw);

                $user->setPassword($password);

                $this->entityManager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été changé.'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Votre mot de passe actuel n\'est pas le bon.'
                );
            }
        }

        return $this->render('account/changePassword.html.twig', [
            'formChangePassword' => $form->createView()
        ]);
    }
}
