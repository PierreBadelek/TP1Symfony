<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';
    public const VIEW = 'USER_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var User $targetUser */
        $targetUser = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($targetUser, $user),
            self::EDIT => $this->canEdit($targetUser, $user),
            self::DELETE => $this->canDelete($targetUser, $user),
            default => false,
        };
    }

    private function canView(User $targetUser, User $user): bool
    {
        // Un utilisateur peut voir son propre profil, ou un ADMIN peut voir tous les profils
        return $targetUser === $user
            || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canEdit(User $targetUser, User $user): bool
    {
        // Seul un ADMIN peut éditer les utilisateurs
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canDelete(User $targetUser, User $user): bool
    {
        // Seul un ADMIN peut supprimer, et pas soi-même
        return in_array('ROLE_ADMIN', $user->getRoles())
            && $targetUser !== $user;
    }
}
