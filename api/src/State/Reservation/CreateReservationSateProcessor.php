<?php

namespace App\State\Reservation;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\ApiResource\Planning;
use App\Entity\Reservation;
use App\Enum\ReservationStatusEnum;
use App\Service\MailerService;
use App\Service\TroubleMakerService;
use App\State\CreateAndUpdateStateProcessor;
use DateTimeImmutable;
use Stripe\StripeClient;
use Symfony\Bundle\SecurityBundle\Security;
use function Symfony\Component\Clock\now;

class CreateReservationSateProcessor implements ProcessorInterface
{
    public function __construct(
        private CreateAndUpdateStateProcessor $createAndUpdateStateProcessor,
        private readonly Security             $security,
        private TroubleMakerService           $troubleMakerService,
        private MailerService $mailerService
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof Post && $data instanceof Reservation) {
            $offset = $this->getOffsetFromDate($data->getDate());
            $troubleMakerPlanning = $this->troubleMakerService->getTroubleMakerPlanning($data->getTroubleMaker()->getId()->toString(), $data->getService()->getId(), $offset, false);
            $planningForDate = $this->getPlanningForDate($data->getDate(), $troubleMakerPlanning);
            if (!$planningForDate) {
                throw new ValidationException('Error');
            }
            if (!$this->isTroubleMakerAvailableAt($data->getDate(), $planningForDate, $data->getService()?->getDuration() ?? 0)) {
                throw new ValidationException('Le créneau n\'est plus disponible.');
            }
            $data->setPaymentIntentId('jzeuoezd');
            $data->setCustomer($this->security->getUser());
            $data->setStatus(ReservationStatusEnum::ACTIVE);
            $data->setPrice($data->getService()?->getPrice());
            $data->setDuration($data->getService()?->getDuration());
            $this->mailerService->sendEmail();
            $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);
        }
    }

    private function getOffsetFromDate(DateTimeImmutable $dateFrom): int
    {
        if ($dateFrom < now()) {
            throw new ValidationException('La date de réservation ne doit pas être inférieure à maintenant.');
        }
        $date = $dateFrom->setTime(0, 0);
        $today = new DateTimeImmutable('today midnight');
        $diffInDays = date_diff($date, $today)->d;

        return floor($diffInDays / 7);
    }

    private function isTroubleMakerAvailableAt(DateTimeImmutable $date, Planning $planning, int $duration): bool
    {
        $startTime = (int)ceil(strtotime($date->format('Y-m-d H:i:s'))/300)*300;
        $endTime = $startTime + $duration;
        foreach ($planning->getShifts() as $shift) {
            if (
                $shift[ 'startTime' ] <= $startTime
                && $shift[ 'endTime' ] >= $endTime
            ) {
                return true;
            }
        }
        return false;
    }

    private function getPlanningForDate(DateTimeImmutable $date, array $plannings): ?Planning
    {
        $formattedDate = $date->format('Y-m-d');
        /**@var $planning Planning*/
        foreach ($plannings as $planning) {
            if ($formattedDate === $planning->getDate()) {
                return $planning;
            }
        }

        return null;
    }
}
