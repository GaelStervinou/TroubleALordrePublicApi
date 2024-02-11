<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Availibility;
use App\Entity\Reservation;
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
        EntityManagerInterface $entityManager,
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private Pagination $pagination
    )
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!($operation instanceof CollectionOperationInterface)) {
            return null;
        }
        //TODO récupérer dispo entreprise + dispo perso et enlever indispo + resa existantes


        $user = $this->entityManager->getRepository(User::class)->find($uriVariables['userId']);
        if(!$user) {
            return null;
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
        if (0 === $offset) {
            $dateTo = $dateFrom->add(new \DateInterval("P7D"));
        } else {
            $dateFrom = (new \DateTimeImmutable())->add(new \DateInterval("P{$offset}D"));
        }

        $userAvailabilities = $availibilityRepository->getTroubleMakerAvailabilityFromDateToDate($user->getId(), $user->getCompany()?->getId(), $dateFrom, $dateTo);

        if (0 === count($userAvailabilities)) {
            //TODO changer null par renvoyer trsversablepaginator vide pareille pour les autres return null
            return null;
        }
        $userUnavailabilities = $unavailabilitiesRepository->getTroubleMakerUnavailabilityFromDateToDate($user->getId(), $dateFrom, $dateTo);
        $reservations = $reservationRepository->getTroubleMakerReservationsFromDateToDate($user->getId(), $dateFrom, $dateTo);
        //dd($userAvailabilities, $reservations, $userUnavailabilities);
        $unavailabilities = $this->getAllUnavailabilities($reservations, $userUnavailabilities);
        $availabilities = $this->sliceShiftsByDays($userAvailabilities, $dateFrom);

        $this->cookThisShit($userUnavailabilities, $availabilities['shifts'], $availabilities['minAndMaxTimes'], $dateFrom, 10);
    }

    private function getAllUnavailabilities(array $reservations, array $userUnavailabilities): array
    {
        $unavailabilities = [];
        /**
         * @var $reservation Reservation
         */
        foreach ($reservations as $reservation) {
            $reservationDuration = $reservation->getDuration();
            $unavailabilities[] = [
                'startTime' => $reservation->getDate(),
                'endTime' => $reservation->getDate()?->add(new \DateInterval("PT{$reservationDuration}S"))
            ];
        }

        /**
         * @var $unavailability Unavailibility
         */
        foreach ($userUnavailabilities as $unavailability) {
            //TODO deal avec le jour
            $unavailabilities[] = [
                'startTime' => $unavailability->getStartTime(),
                'endTime' => $unavailability->getEndTime()
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
        foreach ($availabilities as $availability) {
            $day = $availability?->getDay();
            if (!empty($doneDays) && !in_array((int)$fromDate->format('N'), $doneDays, true)) {
                $fromDate = $fromDate->add(new \DateInterval("P1D"))->setTime(0, 0, 0);
            }
            $date = $fromDate->format('Y-m-d H:i:s');
            if ($day) {
                $startTime = $availability->getCompanyStartTime();
                $endTime = $availability->getCompanyEndTime();
            } else {
                $day = (int)$availability->getStartTime()?->format('N');
                //TODO peut-être spérarer H et i par des ":"
                $startTime = $availability->getStartTime()?->format('H:i');
                $endTime = $availability->getEndTime()?->format('H:i');
            }

            $explodedStartTime = explode(":", $startTime);
            $explodedEndTime = explode(":", $endTime);

            $startTime = $fromDate->setTime((int)$explodedStartTime[0], (int)$explodedStartTime[1]);
            $endTime = $fromDate->setTime((int)$explodedEndTime[0], (int)$explodedEndTime[1]);

            $shifts[$date][] = [
                'startTime' => $startTime,
                'endTime' => $endTime
            ];
            if (!array_key_exists($date, $minAndMaxTimes) || $minAndMaxTimes[$date]['minimumStartTime'] > $startTime) {
                $minAndMaxTimes[$date]['minimumStartTime'] = $startTime;
            }
            if ( !array_key_exists('maximumEndTime', $minAndMaxTimes[$date]) || $minAndMaxTimes[$date]['maximumEndTime'] > $endTime) {
                $minAndMaxTimes[$date]['maximumEndTime'] = $startTime;
            }
            $doneDays[] = $day;
            /*else {
                $startTime = $availability->getStartTime();
                $endTime = $availability->getEndTime();
                if ($startTime->format('H') < $shifts[$startTime->format('N')][]['startTime']) {
                    $shifts[$startTime->format('N')]['startTime'] = $startTime->format('H');
                } else if ($startTime->format('H') > $shifts[$startTime->format('N')]['endTime']) {

                }
            }*/
        }
        return [
            'shifts' => $shifts,
            'minAndMaxTimes' => $minAndMaxTimes
        ];
    }

    private function cookThisShit(array $unavalabilities, array $shifts, array $minAndMaxTimes, \DateTimeImmutable $fromDate, int $duration): array
    {
        for ($i = 1; $i <= 7; $i++) {
            $date = $fromDate->format('Y-m-d H:i:s');
            /**
             * @var $minimumTime \DateTimeImmutable
             */
            $minimumTime = $minAndMaxTimes[$date]['minimumStartTime'];
            $maximumEndTime = $minAndMaxTimes[$date]['maximumEndTime'];
            //TODO faire ça pour arrondir à la dizaine de min au dessus
            //$fullTime = round(strtotime($minimumTime->format('Y-m-d H:i'))/60)*60;
            //$date = \DateTimeImmutable::createFromFormat('U', $fullTime);


            //TODO en gros on va récupérer tous els slots possibles avec getAllPossibleSlots(). ensuite on boucle dessus. On boucle sur les shifts. Si le start time du slot est plus grand que le start time du shift
            //TODO et que le endtime du slot est plus petit que celui du shift, on l'ajoute à un tableau.
            //TODO ensuite, on boucle sur ce tableau et dedans on boucle sur les unavailabilities. Si le start time du slot est plus grand, on le vire, pas besoin de check le endtime. FIN
            /*foreach ($shifts as $day) {
                foreach ($ava)
            }*/



            $fromDate = $fromDate->add(new \DateInterval("P1D"))->setTime(0, 0, 0);
        }
    }

    private function getAllPossibleSlots(\DateTimeImmutable $date, \DateTimeImmutable $minimumTime, \DateTimeImmutable $maximumTime, int $duration): array
    {


    }
}
