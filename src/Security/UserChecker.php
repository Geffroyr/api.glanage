<?php

namespace App\Security;

use Exception;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        if (!$user->isEnabled()) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Unverified email.');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

    }
}
