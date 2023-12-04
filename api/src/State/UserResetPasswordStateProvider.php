<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class UserResetPasswordStateProvider implements ProviderInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?User
    {
        if (array_key_exists('resetPasswordToken', $uriVariables)) {
            return $this->userRepository->findOneBy(['resetPasswordToken' => $uriVariables['resetPasswordToken']]);
        }

        return null;
    }
}
