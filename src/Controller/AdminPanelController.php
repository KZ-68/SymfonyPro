<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRoleFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPanelController extends AbstractController
{
    #[Route(
        '/admin', 
        name: 'app_admin_index', 
        methods:['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminPanelController',
        ]);
    }

    #[Route(
        '/admin/users-list', 
        name: 'app_admin_users_list', 
        methods:['GET']
    )]
    public function usersList(UserRepository $userRepository): Response {

        $users = $userRepository->findAll();

        return $this->render('admin/users-list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route(
        '/admin/users-list/popup-role', 
        name: 'admin_role_popup',
        methods:['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function popupChangeRole(Request $request, UserRepository $userRepository): Response {

        $selectedUser = $request->get("user");

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserRoleFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $selectedRole =  $form->get('roles')->getData();

            // On recherche le bon utilisateur dans la couche modèle
            $editUser = $userRepository->find($selectedUser);

            if ($this->getUser() !== $selectedUser) {
                // On met à jour le rôle par une requête préparée. 
                $userRepository->updateRoleRequest($editUser->getId(), $selectedRole);

                return $this->redirectToRoute('app_admin_users_list');
            } else {
                $this->addFlash('error', 'You can\'t change your own role');

                return $this->redirectToRoute('app_admin_users_list');
            }
            
        }

        return $this->render('admin/_partials/change-role-modal.html.twig', [
                'editUserForm' => $form
            ]
        );
    }

}