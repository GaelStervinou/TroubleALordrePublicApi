<?php

namespace App\State\User;

use App\Entity\User;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class UserMeProvider implements ProviderInterface
{
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?User
    {
        return $this->security->getUser();
        dd($this->security->getUser());
        return $context['user'] instanceof User ? $context['user'] : null;
    }
}