<?php

namespace App\Security\Voter;

use App\Entity\Availability;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateAvailabilityVoter extends Voter
{
    public const CREATE = 'AVAILABILITY_CREATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::CREATE === $attribute
            && $subject instanceof Availability;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /**@var $user User */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        /**@var $subject Availability */
        $company = $subject->getCompany();
        $troubleMaker = $subject->getTroubleMaker();
        if ($company && (
                $user !== $company->getOwner()
            )
        ) {
            return false;
        }

        if ($troubleMaker
            && !($troubleMaker->isTroubleMaker() && $troubleMaker->isActive()
            && $troubleMaker->$user->getOwnedCompanies()->contains($troubleMaker->getCompany())
        )
        ) {
            return false;
        }

        return true;
    }
}
