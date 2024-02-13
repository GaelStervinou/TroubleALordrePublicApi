<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\Service;
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
        return in_array($attribute, [self::CREATE, self::EDIT], true)
            && $subject instanceof Service;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface || !($subject instanceof Service)) {
            return false;
        }
        /**@var $user User*/

        if(!in_array("ROLE_COMPANY_ADMIN", $user->getRoles(), true)) {
            return false;
        }
        $companyId = $subject->getCompany()->getId();

        if (null === $user->getOwnedCompanies()->findFirst(function (Company $company) use($companyId) {
            return $company->getId() === $companyId;
        })) {
            return false;
        }

        return true;
    }
}
