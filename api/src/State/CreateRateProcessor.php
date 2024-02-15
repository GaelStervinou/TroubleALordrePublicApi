<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Rate;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class CreateRateProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private CreateAndUpdateStateProcessor $createAndUpdateStateProcessor
    ){}
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof Post && $data instanceof Rate) {
            $reservation = $data->getReservation();
            if (null === $reservation) {
                throw new ValidationException('Réservation obligatoire');
            }

            $loggedInUser = $this->security->getUser();
            if ($loggedInUser === $reservation->getCustomer()) {
                $data->setRated($reservation->getTroubleMaker());
                $data->setIsTroubleMakerRated(true);
            } elseif ($loggedInUser === $reservation->getTroubleMaker()) {
                $data->setRated($reservation->getCustomer());
                $data->setIsTroubleMakerRated(false);
            } else {
                throw new AccessDeniedException('Vous ne pouvez pas noter une prestation qui ne vous concerne pas.');
            }
            $data->setService($reservation->getService());
            $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);
        }
    }
}
