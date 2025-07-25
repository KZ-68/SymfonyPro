<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class AnonymizeVoter extends Voter
{
    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {

        if (!in_array($attribute, [self::DELETE])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            $vote?->addReason('The user is not logged in.');
            return false;
        }

        $user = $subject;

        return match($attribute) {
            self::DELETE => $this->canDelete($user, $token, $vote),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canDelete(User $user, TokenInterface $token, ?Vote $vote): bool
    {

        if ($this->accessDecisionManager->decide($token, ['ROLE_USER'])) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The user can\'t access to this ressource.',
            $user->getRoles()
        ));

        return false;
    }
}