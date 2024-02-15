<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\ApiResource\Planning;
use App\Entity\Availability;
use App\Entity\Unavailability;
use App\Entity\User;
use App\Repository\AvailabilityRepository;
use App\Repository\UnavailabilityRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UserAvailabilitiesStateProvider implements ProviderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface                                                    $entityManager,
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private Pagination                                                        $pagination,
        private Security                                                          $security
    )
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!($operation instanceof CollectionOperationInterface)) {
            return [null];
        }

        /***@var $user User*/
        $user = $this->entityManager->getRepository(User::class)->find($uriVariables[ 'id' ]);

        if (
            !$user->isTroubleMaker()
            && $this->security->getUser() !== $user->getCompany()->getOwner()
        ) {
            throw new ValidationException("Utilisateur introuvable");
        }

        /**
         * @var $availibilityRepository AvailabilityRepository
         */
        $availibilityRepository = $this->entityManager->getRepository(Availability::class);

        /**
         * @var $unavailabilityRepository UnavailabilityRepository
         */
        $unavailabilityRepository = $this->entityManager->getRepository(Unavailability::class);

        $offset = $this->pagination->getOffset($operation, $context);
        $dateFrom = (new \DateTimeImmutable())->setTime(0, 0)->add(new \DateInterval("P{$offset}D"));
        if (0 !== $offset) {
            $dateFrom = (new \DateTimeImmutable())->add(new \DateInterval("P{$offset}D"));
        }
        $dateTo = $dateFrom->add(new \DateInterval("P7D"));

        $userAvailabilities = $availibilityRepository->getTroubleMakerAvailabilityFromDateToDate($user->getId(), $user->getCompany()?->getId(), $dateFrom, $dateTo);
        if (0 === count($userAvailabilities)) {
            return [];
        }
        $userUnavailabilities = $unavailabilityRepository->getTroubleMakerUnavailabilityFromDateToDate($user->getId(), $dateFrom, $dateTo);

        $userAvailabilitiesSlicedByDay = $this->formatAvailabilitiesByDay($this->sliceShiftsByDays($userAvailabilities, $dateFrom), $dateFrom);
        $userAvailabilitiesByDay = $this->removeUserUnavailabilities($userAvailabilitiesSlicedByDay, $userUnavailabilities, $dateFrom);
        $planningDays = [];
        foreach ($userAvailabilitiesByDay as $day => $slots) {
            $planning = (new Planning())
                ->setDate($day)
                ->setShifts($slots)
                ->formatThisShiftsFromTimestampToString();
            $planningDays[] = $planning;
        }

        return $planningDays;
    }

    private function sliceShiftsByDays(array $availabilities, \DateTimeImmutable $fromDate): array
    {
        $shifts = [];

        /**
         * @var $availability Availability
         */
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
            $doneDays[] = $day;
        }
        return $shifts;
    }

    private function removeUserUnavailabilities(array $availabilities, array $unavailabilities, DateTimeImmutable $fromDate): array
    {
        if (empty($unavailabilities)) {
            return $availabilities;
        }
        $userAvailabilities = [];
        foreach ($availabilities as $date => $availability) {
            foreach ($availability as $index => &$slot) {
                /**@var $unavailability Unavailability*/
                foreach ($unavailabilities as $unavailability) {
                    if ($unavailability->getStartTime()->format('Y-m-d') === DateTimeImmutable::createFromFormat('U', $slot['startTime'])->format('Y-m-d')) {
                        $unavailabilityStartTime = strtotime($unavailability->getStartTime()->format('Y-m-d H:i:s'));
                        $unavailabilityEndTime = strtotime($unavailability->getEndTime()->format('Y-m-d H:i:s'));
                        if (
                            $unavailabilityStartTime < $slot['startTime']
                            && $unavailabilityEndTime > $slot['endTime']
                        ) {
                            break;
                        }
                        if (
                            $unavailabilityStartTime > $slot['startTime']
                            && $unavailabilityStartTime < $slot['endTime']
                            && $unavailabilityEndTime > $slot['endTime']
                        ) {
                            $slot['endTime'] = $unavailabilityStartTime;
                        } elseif (
                            $unavailabilityStartTime < $slot['startTime']
                            && $unavailabilityEndTime > $slot['startTime']
                            && $unavailabilityEndTime < $slot['endTime']
                        ) {
                            $slot['startTime'] = $unavailabilityEndTime;
                        } elseif (
                            $unavailabilityStartTime > $slot['startTime']
                            && $unavailabilityStartTime < $slot['endTime']
                            && $unavailabilityEndTime > $slot['startTime']
                            && $unavailabilityEndTime < $slot['endTime']
                        ) {
                            $availability[] = [
                                'startTime' => $unavailabilityEndTime,
                                'endTime' => $slot['endTime']
                            ];
                            $slot['endTime'] = $unavailabilityStartTime;
                        }
                    }
                    if (!$index) {
                        $userAvailabilities[$date][] = $slot;
                    }
                    $userAvailabilities[$date][$index] = $slot;
                }
            }
        }
        return $userAvailabilities;
    }

    private function formatAvailabilitiesByDay(array $availabilities, DateTimeImmutable $fromDate): array
    {
        $availabilitiesByDay = [];
        for ($i = 1; $i <= 7; $i++) {
            $date = $fromDate->format('Y-m-d');
            if (!array_key_exists($date, $availabilities)) {
                continue;
            }
            foreach ($availabilities[ $date ] as $shift) {
                if (!array_key_exists($date, $availabilitiesByDay)) {
                    $availabilitiesByDay[ $date ][] = $shift;
                } else {
                    foreach ($availabilitiesByDay[ $date ] as $index => $availability) {
                        if (
                            $availability[ 'startTime' ] > $shift[ 'startTime' ]
                            && $availability[ 'endTime' ] > $shift[ 'endTime' ]
                            && $availability[ 'startTime' ] < $shift[ 'endTime' ]
                        ) {
                            $availabilitiesByDay[ $date ][ $index ][ 'startTime' ] = $shift[ 'startTime' ];
                            continue;
                        } elseif (
                            $availability[ 'startTime' ] < $shift[ 'startTime' ]
                            && $availability[ 'endTime' ] > $shift[ 'startTime' ]
                            && $availability[ 'endTime' ] < $shift[ 'endTime' ]
                        ) {
                            $availabilitiesByDay[ $date ][ $index ][ 'endTime' ] = $shift[ 'endTime' ];
                            continue;
                        } elseif (
                            $availability[ 'startTime' ] > $shift[ 'startTime' ]
                            && $availability[ 'endTime' ] < $shift[ 'endTime' ]
                        ) {
                            $availabilitiesByDay[ $date ][ $index ] = $shift;
                            continue;
                        } elseif (
                            $availability[ 'startTime' ] < $shift[ 'startTime' ]
                            && $availability[ 'endTime' ] > $shift[ 'endTime' ]
                        ) {
                            continue;
                        }
                        $availabilitiesByDay[ $date ][] = $shift;
                    }
                }
            }
            $fromDate = $fromDate->add(new \DateInterval("P1D"))->setTime(0, 0, 0);
        }

        return $availabilitiesByDay;
    }
}
