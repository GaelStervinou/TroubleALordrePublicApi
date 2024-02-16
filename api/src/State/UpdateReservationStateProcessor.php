<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Reservation;
use App\Enum\ReservationStatusEnum;

class UpdateReservationStateProcessor implements ProcessorInterface
{
    public function __construct(
        private CreateAndUpdateStateProcessor $createAndUpdateStateProcessor
    ){}
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof Patch && $data instanceof Reservation) {
            if ($context[ 'previous_data' ]->getStatus() === ReservationStatusEnum::CANCELED->value) {
                throw new ValidationException("Cette réservation est annulée, vous ne pouvez pas la modifier.");
            }
            if ($data->getStatus() === ReservationStatusEnum::REFUNDED->value) {
                throw new ValidationException("Cette action est impossible.");
            }
            $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);
        }
    }
}
