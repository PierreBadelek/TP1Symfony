<?php

namespace App\Security\Voter;

use App\Entity\Exemplaire;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ExemplaireVoter extends Voter
{
    public const EDIT = 'EXEMPLAIRE_EDIT';
    public const DELETE = 'EXEMPLAIRE_DELETE';
    public const CREATE = 'EXEMPLAIRE_CREATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::CREATE])
            && ($subject instanceof Exemplaire || $subject === null);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Seuls les LIBRARIAN et ADMIN peuvent gérer les exemplaires
        return in_array('ROLE_LIBRARIAN', $user->getRoles())
            || in_array('ROLE_ADMIN', $user->getRoles());
    }
}
