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
        return in_array($attribute, [self::CREATE, self::EDIT], true) && $subject instanceof Service;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface || $subject instanceof Service) {
            return false;
        }
        /**@var $user User*/
        if (!$user->isCompanyAdmin()) {
            return false;
        }
        /**@var $subject Service*/

        return $user === $subject->getCompany()?->getOwner() && $subject->getCompany()?->isActive();
    }
}
