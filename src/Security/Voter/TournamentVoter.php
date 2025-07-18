<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TournamentVoter extends Voter
{
    public const SHOW = 'TOURNAMENT_SHOW';
    public const UPDATE = 'TOURNAMENT_UPDATE';
    public const DELETE = 'TOURNAMENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::SHOW, self::UPDATE, self::DELETE])
            && $subject instanceof \App\Entity\Tournament;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::SHOW:
                // logic to determine if the user can EDIT
                // return true or false
                if($subject->getCreatedBy() === $user) { return true; }
                break;

            case self::UPDATE:
                if($subject->getCreatedBy() === $user || in_array('ROLE_ADMIN', $user->getRoles())) { return true; }
                // logic to determine if the user can VIEW
                // return true or false
                break;

            case self::DELETE:
                /** @var Tournament $subject */
                if($subject->getCreatedBy() === $user || in_array('ROLE_ADMIN', $user->getRoles())) { return true; }
                break;
        }

        return false;
    }
}
