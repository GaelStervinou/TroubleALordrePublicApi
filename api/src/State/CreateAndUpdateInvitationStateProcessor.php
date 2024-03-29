<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Invitation;
use App\Entity\User;
use App\Enum\UserStatusEnum;
use App\Repository\UserRepository;

class CreateAndUpdateInvitationStateProcessor implements ProcessorInterface
{
    private UserRepository $userRepository;
    private ?CreateAndUpdateStateProcessor $createAndUpdateStateProcessor = null;

    public function __construct(
        UserRepository                $userRepository,
        CreateAndUpdateStateProcessor $createAndUpdateStateProcessor
    )
    {
        $this->userRepository = $userRepository;
        $this->createAndUpdateStateProcessor = $createAndUpdateStateProcessor;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof Post && $data instanceof Invitation) {
            $user = $this->userRepository->findOneBy([
                'email' => $context[ 'request' ]->get('email'),
                'status' => UserStatusEnum::USER_STATUS_ACTIVE->value,
            ]);
            if ($user?->isTroubleMaker() && $this->isFirstInvitationFromCompanyToUser($user, $data)) {
                $data->setReceiver($user);
                //TODO écouter l'event de création et quand c'est créer, envoyer un mail ? ou l'envoyer direct ici
                $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);
            } else {
                throw new ValidationException("Utilisateur introuvable.");
            }
        }
    }

    private function isFirstInvitationFromCompanyToUser(User $user, Invitation $currentInvitation): bool
    {
        return !$user->getInvitations()->exists(
            function (Invitation $invitation) use ($currentInvitation) {
            return $currentInvitation->getCompany() === $invitation->getCompany() && $currentInvitation->isPending();
        });
    }
}
