<?php

namespace App\State\Reservation;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Reservation;
use App\Enum\ReservationStatusEnum;
use App\State\CreateAndUpdateStateProcessor;
use Stripe\StripeClient;
use Symfony\Bundle\SecurityBundle\Security;

class CreateReservationSateProcessor implements ProcessorInterface
{
    public function __construct(private CreateAndUpdateStateProcessor $createAndUpdateStateProcessor, private readonly Security $security)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof Post && $data instanceof Reservation){

            //TODO check que le créneau est tjs dispo. Extraire la logique du state provider du planning pr un user et un service afin de vérifier.
            $data->setCustomer($this->security->getUser());
            $data->setStatus(ReservationStatusEnum::ACTIVE);
            $data->setPrice($data->getService()?->getPrice());
            $data->setDuration($data->getService()?->getDuration());
            $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);
        }
    }
}
