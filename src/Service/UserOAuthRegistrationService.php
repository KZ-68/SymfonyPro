<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserOAuthRegistrationService {

    public function createUserFromForm(
        FormInterface $form, 
        User $user, 
        UserResponseInterface $userInformation,
        Request $request
    ): ?bool
    {
        $user->setEmail($userInformation->getEmail());
        $user->setUsername($userInformation->getNickname());
        $user->setFirstName($userInformation->getFirstName());
        $user->setLastName($userInformation->getLastName());

        $form->setData($user);
        $form->handleRequest($request);

        $agreeTerms = $form->get('agreeTerms')->getData();
        if($agreeTerms === true) {
            $user->setAgreedTerms(true);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            return true;
        }
        return false;
    }


}