<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRegistrationService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em,
    ) {}

    public function register(User $user, string $plainPassword): void
    {   
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $user->setAgreedTerms(true);
        $this->em->persist($user);
        $this->em->flush();
    }
}