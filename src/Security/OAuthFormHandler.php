<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Form\RegistrationFormHandlerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class OAuthFormHandler implements RegistrationFormHandlerInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function process(Request $request, FormInterface $form, UserResponseInterface $userInformation): bool
    {
        $user = new User();
        $user->setEmail($userInformation->getEmail());
        $user->setUsername($userInformation->getNickname());
        $user->setFirstName($userInformation->getFirstName());
        $user->setLastName($userInformation->getLastName());

        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/"; 
            $plainPassword = $form->get('plainPassword')->getData();
            if(preg_match($password_regex, $plainPassword)) {
                $agreeTerms = $form->get('agreeTerms')->getData();
                if($agreeTerms === true) {
                    // encode the plain password
                    $user->setPassword(
                        $this->userPasswordHasher->hashPassword(
                            $user,
                            $plainPassword
                        )
                    );

                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        return false;
    }
}