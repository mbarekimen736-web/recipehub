<?php

namespace App\Security;

use App\Entity\Recette;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RecetteVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        if (! $subject instanceof Recette) {
            return false;
        }

        return true;
    }

    /** @param Recette $subject */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (! $user instanceof User) {
            return false;
        }

        // admins can do anything
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                return $subject->getAuteur() && $subject->getAuteur()->getId() === $user->getId();
        }

        return false;
    }
}
