<?php

namespace App\Tests\Unit;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AnonymizeUserTest extends KernelTestCase
{
    public function testAnonymization(): void
    { 
        self::bootKernel();

        $em = static::getContainer()->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);
        
        $user = $userRepository->findOneBy(['id' => 3]);
        $userId = $user->getId();
        $userRepository->anonymizeUser($userId);

        $em->refresh($user);
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getFirstName());
        $this->assertNull($user->getLastName());
    }
}