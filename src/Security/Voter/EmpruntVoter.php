<?php

namespace App\Security\Voter;

use App\Entity\Emprunt;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EmpruntVoter extends Voter
{
    public const RETOUR = 'EMPRUNT_RETOUR';
    public const RENOUVELER = 'EMPRUNT_RENOUVELER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::RETOUR, self::RENOUVELER])
            && $subject instanceof Emprunt;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Emprunt $emprunt */
        $emprunt = $subject;

        return match ($attribute) {
            self::RETOUR => $this->canRetour($emprunt, $user),
            self::RENOUVELER => $this->canRenouveler($emprunt, $user),
            default => false,
        };
    }

    private function canRetour(Emprunt $emprunt, User $user): bool
    {
        // Le propriétaire de l'emprunt ou un LIBRARIAN/ADMIN peut retourner
        return $emprunt->getUser() === $user
            || in_array('ROLE_LIBRARIAN', $user->getRoles())
            || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canRenouveler(Emprunt $emprunt, User $user): bool
    {
        // Seul le propriétaire peut renouveler
        return $emprunt->getUser() === $user;
    }
}
