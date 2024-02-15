<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Invitation;
use App\Entity\User;
use App\Enum\InvitationStatusEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;

class UpdateInvitationStateProcessor implements ProcessorInterface
{
    private ?SoftDeleteStateProcessor $softDeleteStateProcessor = null;
    private Security $security;

    public function __construct(
        SoftDeleteStateProcessor $createAndUpdateStateProcessor,
        Security                 $security
    )
    {
        $this->softDeleteStateProcessor = $createAndUpdateStateProcessor;
        $this->security = $security;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof Patch && $data instanceof Invitation) {
            if (!$context['previous_data']->isPending()) {
                throw new ValidationException("Cette invitation a déjà été accepté, refusée ou annulée.");
            }
            $status = $data->getStatus();
            /**@var $loggedInUser User */
            $loggedInUser = $this->security->getUser();
            if (
                InvitationStatusEnum::ACCEPTED->value === $status
                && $loggedInUser === $data->getReceiver()
            ) {
                //TODO rajouter la company au user ( sûrement via cascade update )
                $data->getReceiver()?->setCompany($data->getCompany());
                $this->softDeleteStateProcessor->process($data, $operation, $uriVariables, $context);
            } elseif (
                (InvitationStatusEnum::REFUSED->value === $status
                    && $loggedInUser === $data->getReceiver())
                || (InvitationStatusEnum::CANCELED->value === $status
                    && $loggedInUser === $data->getCompany()?->getOwner())
            ) {
                $this->softDeleteStateProcessor->process($data, $operation, $uriVariables, $context);
            } else {
                throw new ValidationException("Vous ne pouvez pas choisir ce statut.");
            }
        }
    }
}
