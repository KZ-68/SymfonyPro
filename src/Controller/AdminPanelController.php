<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPanelController extends AbstractController
{
    #[Route(
        'admin', 
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
        'admin_users_list', 
        name: 'app_admin_users_list', 
        methods:['GET']
    )]
    public function usersList(UserRepository $userRepository): Response {

        $users = $userRepository->findAll();

        return $this->render('admin/users-list.html.twig', [
            'users' => $users,
        ]);
    }
}