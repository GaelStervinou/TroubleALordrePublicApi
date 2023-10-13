<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Enum\UserStatusEnum;
use App\Entity\User;
//use App\Service\MailerService;
use App\Service\MailerService;
use Exception;
use http\Exception\InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($this->isUpdatingPassword($data)) {
            if ($data->getPlainPassword() !== $data->getVerifyPassword()) {
                throw new InvalidArgumentException('Les mots de passe ne correspondent pas.');
            }
            $data->setPassword($this->passwordHasher->hashPassword(
                $data,
                $data->getPlainPassword()
            ));
            $data->eraseCredentials();
        }
        if ($operation instanceof Post) {
            $data->setStatus(UserStatusEnum::USER_STATUS_PENDING->value);
            $data->setRoles(['ROLE_USER']);

            //TODO gérer sendinblue ( créer compte + reprendre le template de la route du trone )
            /*$token = $this->mailerService::sendEmail(
            [
                'emailTo' => $data->getEmail(),
                'lastnameTo' => $data->getLastname(),
                'firstnameTo' => $data->getFirstname()
            ],
            MailerService::VERIFY_ACCOUNT_TEMPLATE_ID
            );
            if (!$token) {
                throw new InvalidArgumentException('Une erreur est survenue lors de l\'envoi du mail de validation.');
            }
            $data->setValidationToken($token);*/
            $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);
        } elseif ($operation instanceof Patch) {
            if ($context[ 'previous_data' ]->getEmail() !== $data->getEmail()) {
                $data->setStatus(UserStatusEnum::USER_STATUS_PENDING->value);
            }
            $this->softDeleteStateProcessor->process($data, $operation, $uriVariables, $context);
        }
    }

    /**
     * @param mixed $data
     * @return bool
     */
    public function isUpdatingPassword(mixed $data): bool
    {
        return $data->getPlainPassword() !== null && $data->getVerifyPassword() !== null;
    }
}