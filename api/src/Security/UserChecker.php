<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {

    }

    public function checkPostAuth(UserInterface $user): void
    {
        if ($user->isPending()) {
            throw new CustomUserMessageAuthenticationException('Un email de confirmation vous a été envoyez, veuillez confimer votre compte');
        }
        if (!$user->isActive()) {
            throw new BadCredentialsException();
        }
    }
}
