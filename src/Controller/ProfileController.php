<?php

namespace App\Controller;

use App\Form\UserPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileController extends AbstractController
{
    #[Route('profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route(
        'profile/edit-password', 
        name: 'edit_password',
        methods:['GET', 'POST']
    )]
    #[IsGranted('ROLE_USER')]
    public function editPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserPasswordFormType::class, $user);

        $form->handleRequest($request);
            // Si le formulaire n°2 est soumis et valide
            if ($form->isSubmitted() && $form->isValid()) {
                // On récupère les données du mot de passe courant et le nouveau que l'on souhaite
                $oldPassword = $form->get('plainPassword')->getData();
                $newPassword = $form->get('newPassword')->getData();
                // Si l'ancien mot de passe et le nouveau ne sont pas identique :
                if ($oldPassword != $newPassword) {
                    // Si le mot de passe courant correspond au mot de passe hashé en bdd :
                    if ($hasher->isPasswordValid($user, $oldPassword)) {
                        /* On déclare une variable qui prends comme valeur 
                        un nouveau hashage créé avec le nouveau mot de passe soumis */
                        $encodedPassword = $hasher->hashPassword(
                            $user,
                            $newPassword
                        );
                        // On ajoute le mot de passe à l'utilisateur
                        $user->setPassword($encodedPassword);
                        // On prépare l'envoi en bdd
                        $entityManager->persist($user);
                        // On envoie les données en bdd
                        $entityManager->flush();

                        $this->addFlash(
                            'success', 
                            'The password has been modified with success !'
                        );
                        
                        return $this->redirectToRoute('app_profile');
                        
                    } else {
                        $this->addFlash(
                            'warning', 
                            'The informations submited are incorrects.'
                        );
                    }
                } else {
                    $this->addFlash(
                        'warning',
                        'Old and new password need to be different'
                    );
                }  
            }

        return $this->render('profile/edit-password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(
        '/profile/settings/popup', 
        name: 'settings_delete_popup',
        methods:['GET', 'POST']
    )]
    #[IsGranted('ROLE_USER')]
    public function popupDeleteAccount(Request $request): Response {

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/_partials/anonymize-profile-modal.html.twig');
    }

    #[Route(
        '/profile/settings/popup/delete', 
        name: 'settings_delete_profile', 
        methods:['POST']
    )]
    #[IsGranted('delete', 'user', 'Access Denied', 403)]
    public function deleteAccount(Request $request, UserRepository $userRepository, Session $session, TokenStorageInterface $tokenStorage): Response {

        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $userRepository->anonymizeUser($user->getId());
            $tokenStorage->setToken(null);
            $session->invalidate();
            return $this->redirectToRoute('app_home');
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}