<?php

namespace App\Security\Voter;

use App\Entity\Ouvrage;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OuvrageVoter extends Voter
{
    public const EDIT = 'OUVRAGE_EDIT';
    public const DELETE = 'OUVRAGE_DELETE';
    public const CREATE = 'OUVRAGE_CREATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::CREATE])
            && ($subject instanceof Ouvrage || $subject === null);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Seuls les LIBRARIAN et ADMIN peuvent gérer les ouvrages
        return in_array('ROLE_LIBRARIAN', $user->getRoles())
            || in_array('ROLE_ADMIN', $user->getRoles());
    }
}
