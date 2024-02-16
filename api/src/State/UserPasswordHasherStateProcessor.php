<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Enum\UserStatusEnum;
use App\Entity\User;
use App\Service\UserService;
use Exception;
use http\Exception\InvalidArgumentException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\MailerService;

final class UserPasswordHasherStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly CreateAndUpdateStateProcessor $createAndUpdateStateProcessor,
        private readonly SoftDeleteStateProcessor $softDeleteStateProcessor,
        private readonly MailerService $mailerService,
        private readonly Security $security
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
            if (
                !$this->security->isGranted('ROLE_ADMIN')
                && ((
                    $data->isCompanyAdmin()
                    && !$context[ 'previous_data' ]->isCompanyAdmin()
                )
                || (
                    $data->isAdmin()
                    && !$context[ 'previous_data' ]->isAdmin()
                ))
            ) {
                throw new ValidationException("Vous ne pouvez pas changer votre rôle.");
            }
            $data = $userService->updateUser($context[ 'previous_data' ]->getEmail() !== $data->getEmail());
            $this->softDeleteStateProcessor->process($data, $operation, $uriVariables, $context);
        }
    }
}