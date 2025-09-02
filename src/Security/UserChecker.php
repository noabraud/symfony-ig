<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        // Non utilisé ici
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return; // ignore si ce n’est pas notre entité User
        }

        if (!$user->isVerified()) {
            // Bloque la connexion avec message d'erreur
            throw new CustomUserMessageAuthenticationException(
                'Veuillez vérifier votre adresse e-mail avant de vous connecter.'
            );
        }
    }
}
