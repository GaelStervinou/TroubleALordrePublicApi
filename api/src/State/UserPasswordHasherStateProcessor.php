<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Enum\UserStatusEnum;
use App\Entity\User;
use App\Service\UserService;
use Exception;
use http\Exception\InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\MailerService;

final class UserPasswordHasherStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly CreateAndUpdateStateProcessor $createAndUpdateStateProcessor,
        private readonly SoftDeleteStateProcessor $softDeleteStateProcessor,
        private readonly MailerService $mailerService
    )
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$data instanceof User) {
            return;
        }
        $userService = new UserService($data, $this->passwordHasher, $this->mailerService);

        if ($operation instanceof Post) {
            $data = $userService->createUser();

            $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);
        } elseif ($operation instanceof Patch) {
            $data = $userService->updateUser($context[ 'previous_data' ]->getEmail() !== $data->getEmail());
            $this->softDeleteStateProcessor->process($data, $operation, $uriVariables, $context);
        }
    }
}