<?php

namespace App\Tests\Integration\Services;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Service\UserRegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationServiceTest extends KernelTestCase
{
    public function testUserRegistrationForm(): void
    {
        self::bootKernel();

        $user = new User();
        $user->setEmail('test@example.com');
        $plainPassword = 'securepassword';
        $hashedPassword = 'hashed_securepassword';

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, $plainPassword)
            ->willReturn($hashedPassword);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist')->with($user);
        $em->expects($this->once())->method('flush');

        $emailVerifier = $this->createMock(EmailVerifier::class);
        $emailVerifier->expects($this->once())
            ->method('sendEmailConfirmation')
            ->with(
                'app_verify_email',
                $user,
                $this->callback(function (TemplatedEmail $email) {
                    return 
                        $email->getFrom()[0]->getAddress() === 'admin@test.com' &&
                        $email->getSubject() === 'Please Confirm your Email' &&
                        $email->getHtmlTemplate() === 'registration/confirmation_email.html.twig';
                })
            );

        // Act
        $registrationService = new UserRegistrationService($passwordHasher, $em, $emailVerifier);
        $registrationService->register($user, $plainPassword);

        // Assert
        $this->assertEquals($hashedPassword, $user->getPassword());
        $this->assertTrue($user->hasAgreedTerms());
        $this->assertFalse($user->isVerified());
    }
}