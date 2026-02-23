<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        
        return in_array($attribute, ['ADMIN_ACCESS', 'CUSTOMER_ACCESS']);
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
        case 'ADMIN_ACCESS':
            // admin a accès à la gestion des utilisateurs
            return in_array('ROLE_ADMIN', $user->getRoles());
        
        case 'CUSTOMER_ACCESS':
            // Admin ET  manager peuvent gérer les clients
            return in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_MANAGER', $user->getRoles());
    }

        return false;
    }
}
