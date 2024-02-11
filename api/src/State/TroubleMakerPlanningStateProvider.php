<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Planning;
use App\Entity\Availibility;
use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\Unavailibility;
use App\Entity\User;
use App\Repository\AvailibilityRepository;
use App\Repository\ReservationRepository;
use App\Repository\UnavailibilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TroubleMakerPlanningStateProvider implements ProviderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface                                                    $entityManager,
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private Pagination                                                        $pagination
    )
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!($operation instanceof CollectionOperationInterface)) {
            return [null];
        }

        $user = $this->entityManager->getRepository(User::class)->find($uriVariables[ 'userId' ]);
        if (!$user) {
            return [];
        }
        $service = $this->entityManager->getRepository(Service::class)->find($uriVariables[ 'serviceId' ]);
        if (!$service) {
            return [];
        }
        $offset = $this->pagination->getOffset($operation, $context);
        /**
         * @var $availibilityRepository AvailibilityRepository
         */
        $availibilityRepository = $this->entityManager->getRepository(Availibility::class);
        /**
         * @var $unavailabilitiesRepository UnavailibilityRepository
         */
        $unavailabilitiesRepository = $this->entityManager->getRepository(Unavailibility::class);
        /**
         * @var $reservationRepository ReservationRepository
         */
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);


        $dateFrom = (new \DateTimeImmutable())->add(new \DateInterval("P{$offset}D"));
        if (0 !== $offset) {
            $dateFrom = (new \DateTimeImmutable())->add(new \DateInterval("P{$offset}D"));
        }
        $dateTo = $dateFrom->add(new \DateInterval("P7D"));

        $userAvailabilities = $availibilityRepository->getTroubleMakerAvailabilityFromDateToDate($user->getId(), $user->getCompany()?->getId(), $dateFrom, $dateTo);

        if (0 === count($userAvailabilities)) {
            return [];
        }
        $userUnavailabilities = $unavailabilitiesRepository->getTroubleMakerUnavailabilityFromDateToDate($user->getId(), $dateFrom, $dateTo);
        $reservations = $reservationRepository->getTroubleMakerReservationsFromDateToDate($user->getId(), $dateFrom, $dateTo);
        $unavailabilities = $this->getAllUnavailabilities($reservations, $userUnavailabilities);
        $availabilities = $this->sliceShiftsByDays($userAvailabilities, $dateFrom);
        $availableSlotsByDay = $this->cookThisShit($unavailabilities, $availabilities[ 'shifts' ], $availabilities[ 'minAndMaxTimes' ], $dateFrom, $service->getDuration());

        $planningDays = [];
        foreach ($availableSlotsByDay as $day => $slots) {
            $planning = (new Planning())
                ->setDate($day)
                ->setShifts($slots)
                ->formatThisShiftsFromTimestampToString()
            ;
            $planningDays[] = $planning;
        }

        return $planningDays;
    }

    private function getAllUnavailabilities(array $reservations, array $userUnavailabilities): array
    {
        $unavailabilities = [];
        /**
         * @var $reservation Reservation
         */
        foreach ($reservations as $reservation) {
            $reservationDuration = $reservation->getDuration();
            $reservationDate = $reservation->getDate();
            $unavailabilities[ $reservationDate?->format('Y-m-d') ][] = [
                'startTime' => strtotime($reservationDate?->format('Y-m-d H:i:s')),
                'endTime' => strtotime($reservation->getDate()?->add(new \DateInterval("PT{$reservationDuration}S"))?->format('Y-m-d H:i:s'))
            ];
        }

        /**
         * @var $unavailability Unavailibility
         */
        foreach ($userUnavailabilities as $unavailability) {
            $startTime = $unavailability->getStartTime();
            $unavailabilities[ $startTime?->format('Y-m-d') ][] = [
                'startTime' => strtotime($startTime?->format('Y-m-d H:i:s')),
                'endTime' => strtotime($unavailability->getEndTime()?->format('Y-m-d H:i:s'))
            ];
        }

        return $unavailabilities;
    }

    private function sliceShiftsByDays(array $availabilities, \DateTimeImmutable $fromDate): array
    {
        $shifts = [];

        /**
         * @var $availability Availibility
         */
        $minAndMaxTimes = [];
        $doneDays = [];
        $dateImmutable = $fromDate;
        $date = $dateImmutable->format('Y-m-d');
        foreach ($availabilities as $availability) {
            $day = $availability?->getDay();
            if ($day) {
                if (!empty($doneDays) && !in_array($day, $doneDays, true)) {
                    $dateImmutable = $dateImmutable->add(new \DateInterval("P1D"));
                    $date = $dateImmutable->format('Y-m-d');
                }
                $startTime = $availability->getCompanyStartTime();
                $endTime = $availability->getCompanyEndTime();
            } else {
                $day = (int)$availability->getStartTime()?->format('N');
                $date = $availability->getStartTime()->format('Y-m-d');
                //TODO peut-être spérarer H et i par des ":"
                $startTime = $availability->getStartTime()?->format('H:i');
                $endTime = $availability->getEndTime()?->format('H:i');
            }

            $explodedStartTime = explode(":", $startTime);
            $explodedEndTime = explode(":", $endTime);

            $startTime = strtotime($fromDate->setTime((int)$explodedStartTime[ 0 ], (int)$explodedStartTime[ 1 ])->format('Y-m-d H:i:s'));
            $endTime = strtotime($fromDate->setTime((int)$explodedEndTime[ 0 ], (int)$explodedEndTime[ 1 ])->format('Y-m-d H:i:s'));

            $shifts[ $date ][] = [
                'startTime' => $startTime,
                'endTime' => $endTime
            ];
            if (!array_key_exists($date, $minAndMaxTimes) || $minAndMaxTimes[ $date ][ 'minimumStartTime' ] > $startTime) {
                $minAndMaxTimes[ $date ][ 'minimumStartTime' ] = $startTime;
            }
            if (!array_key_exists('maximumEndTime', $minAndMaxTimes[ $date ]) || $minAndMaxTimes[ $date ][ 'maximumEndTime' ] > $endTime) {
                $minAndMaxTimes[ $date ][ 'maximumEndTime' ] = $endTime;
            }
            $doneDays[] = $day;
        }
        return [
            'shifts' => $shifts,
            'minAndMaxTimes' => $minAndMaxTimes
        ];
    }

    private function cookThisShit(array $unavalabilities, array $shifts, array $minAndMaxTimes, \DateTimeImmutable $fromDate, int $duration): array
    {
        $avalaibleSlotsByDay = [];
        for ($i = 1; $i <= 7; $i++) {
            $date = $fromDate->format('Y-m-d');
            $minimumTime = $minAndMaxTimes[ $date ][ 'minimumStartTime' ];
            $maximumEndTime = $minAndMaxTimes[ $date ][ 'maximumEndTime' ];
            //TODO faire ça pour arrondir à la dizaine de min au dessus
            //$fullTime = round(strtotime($minimumTime->format('Y-m-d H:i'))/60)*60;
            //$date = \DateTimeImmutable::createFromFormat('U', $fullTime);

            $slots = $this->getAllPossibleSlotsByDay($minimumTime, $maximumEndTime, $duration);

            $possibleSlots = [];
            if (array_key_exists($date, $shifts)) {
                foreach ($slots as $index => $slot) {
                    foreach ($shifts[ $date ] as $shift) {
                        if ($shift[ 'startTime' ] < $slot[ 'startTime' ] && $shift[ 'endTime' ] > $slot[ 'endTime' ]) {
                            $possibleSlots[] = $slot;
                            break;
                        }
                    }
                }
            }

            if (array_key_exists($date, $unavalabilities)) {
                foreach ($possibleSlots as $index => $slot) {
                    foreach ($unavalabilities[ $date ] as $unavalability) {
                        if ($slot[ 'startTime' ] > $unavalability[ 'startTime' ] && $slot[ 'startTime' ] < $unavalability[ 'endTime' ]) {
                            unset($possibleSlots[ $index ]);
                        }
                    }
                }
            }

            $avalaibleSlotsByDay[ $date ] = $possibleSlots;

            $fromDate = $fromDate->add(new \DateInterval("P1D"))->setTime(0, 0, 0);
        }

        return $avalaibleSlotsByDay;
    }

    private function getAllPossibleSlotsByDay(int $minimumTime, int $maximumTime, int $duration): array
    {
        $timeRange = $maximumTime - $minimumTime;
        $slots = [];

        $numberOfSlots = floor($timeRange / $duration);

        for ($i = 0; $i < $numberOfSlots; $i++) {
            $slots[] = [
                'startTime' => $minimumTime + ($duration * $i),
                'endTime' => +$minimumTime + ($duration * $i) + $duration
            ];
        }

        return $slots;

    }
}
