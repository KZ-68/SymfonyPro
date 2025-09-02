<?php

namespace App\EventListener;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: 'postPersist')]
class EmailVerificationLinkListener
{
    public function __construct(
        private EmailVerifier $emailVerifier
    ) {}

    public function postPersist(PostPersistEventArgs $args): void
    {

        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        $isVerified = $user->isVerified();

        if($isVerified === false) {
            $this->sendMail($user);
        }
    }

    public function sendMail(User $user): void {
        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('admin@test.com', 'Admin Mail'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}