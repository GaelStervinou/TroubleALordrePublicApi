<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\Service;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ServiceCreationVoter extends Voter
{
    public const CREATE = 'SERVICE_CREATE';
    public const EDIT = 'SERVICE_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CREATE, self::EDIT], true) && $subject instanceof Service;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        /**@var $user User*/
        if (!$user->isCompanyAdmin()) {
            return false;
        }

        return $user === $subject->getCompany()?->getOwner();
    }
}
