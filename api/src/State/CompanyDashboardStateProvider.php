<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\CompanyDashboard;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class CompanyDashboardStateProvider implements ProviderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return [];
        }

        $companyDashboard = new CompanyDashboard();
        /**
         * @var $reservationRepository ReservationRepository
         */
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);
        $todayDate = new \DateTimeImmutable();


        $previousMonthDateFrom = \DateTimeImmutable::createFromFormat('U', strtotime("-2 months", strtotime($todayDate->setTime(0, 0)->format('Y-m-d H:i:s'))));
        $previousMonthDateTo = \DateTimeImmutable::createFromFormat('U', strtotime("-1 month", strtotime($todayDate->setTime(23, 59)->format('Y-m-d H:i:s'))));

        $previousMonthReservations = $reservationRepository->getCompanyReservationsFromDateToDate(
            $previousMonthDateFrom,
            $previousMonthDateTo,
            $uriVariables[ 'id' ]
        );

        $companyDashboard->setNumberOfReservationsPreviousMonth(count($previousMonthReservations));
        $companyDashboard->setMonthSalesNumberPreviousMonth($this->calculateMonthSales($previousMonthReservations));

        $currentMonthDateTo = \DateTimeImmutable::createFromFormat('U', strtotime("-1 month", strtotime($todayDate->setTime(0, 0)->format('Y-m-d H:i:s'))));

        $currentMonthReservations = $reservationRepository->getCompanyReservationsFromDateToDate(
            $currentMonthDateTo,
            $todayDate,
            $uriVariables[ 'id' ]
        );

        $companyDashboard->setId($uriVariables[ 'id' ]);

        $companyDashboard->setMonthsSalesAmountCurrentMonth($this->calculateMonthSales($currentMonthReservations));
        $formattedReservationsAndSalesAmountCurrentMonth = $this->associateDayOfMonthToReservationNumberAndSalesAmount($currentMonthReservations, $currentMonthDateTo, $todayDate, 0);
        $companyDashboard->setReservationsCurrentMonth($formattedReservationsAndSalesAmountCurrentMonth[ 'reservationsByDate' ]);
        $companyDashboard->setMonthSalesCurrentMonth($formattedReservationsAndSalesAmountCurrentMonth[ 'salesAmountByDate' ]);
        $bestTroubleMakerId = $reservationRepository->getCompanyBestTroubleMakerFromDateToDate(
            \DateTimeImmutable::createFromFormat('U', strtotime("-1 month", strtotime($todayDate->setTime(0, 0)->format('Y-m-d H:i:s')))),
            $todayDate,
            $uriVariables[ 'id' ]
        )[0]['id'];
        $bestTroubleMaker = $this->entityManager->getRepository(User::class)->find($bestTroubleMakerId->serialize());
        return $companyDashboard;
    }

    private function calculateMonthSales(array $reservations): float
    {
        return array_reduce($reservations, static function ($amount, $reservation) {
            return $amount + $reservation->getPrice();
        }, 0);

    }

    private function associateDayOfMonthToReservationNumberAndSalesAmount(array $reservations, \DateTimeImmutable $dateFrom, \DateTimeImmutable $dateTo, mixed $value): array
    {
        $finalArrayNumberOfReservations = [];
        $finalArraySalesAmount = [];
        /**
         * @var $reservation Reservation
         */
        foreach ($reservations as $reservation) {
            $date = $reservation->getDate()?->format('Y-m-d');
            if (array_key_exists($date, $finalArrayNumberOfReservations)) {
                $finalArrayNumberOfReservations[ $date ]++;
            } else {
                $finalArrayNumberOfReservations[ $date ] = 1;
            }

            if (array_key_exists($date, $finalArraySalesAmount)) {
                $finalArraySalesAmount[ $date ] += $reservation->getPrice();
            } else {
                $finalArraySalesAmount[ $date ] = $reservation->getPrice();
            }
        }

        return [
            'reservationsByDate' => $this->fillMissingDaysWithSpecificValue($finalArrayNumberOfReservations, $dateFrom, $dateTo, $value),
            'salesAmountByDate' => $this->fillMissingDaysWithSpecificValue($finalArraySalesAmount, $dateFrom, $dateTo, $value)
        ];
    }

    private function fillMissingDaysWithSpecificValue(array $datas, \DateTimeImmutable $dateFrom, \DateTimeImmutable $dateTo, mixed $value): array
    {
        $dateFromToString = $dateFrom->format('Y-m-d');
        $dateToToTime = strtotime($dateTo->format('Y-m-d'));
        $dateFromToTime = strtotime($dateFromToString);
        $diffInDays = round(($dateToToTime - $dateFromToTime) / (60 * 60 * 24));

        $orderedByDateDatas = [];
        for ($i=0; $i<=$diffInDays; $i++) {
            if(!array_key_exists($dateFromToString, $datas)) {
                $orderedByDateDatas[$dateFromToString] = $value;
            } else {
                $orderedByDateDatas[$dateFromToString] = $datas[$dateFromToString];
            }
            $dateFrom =  $dateFrom->add(new \DateInterval('P1D'));
            $dateFromToString = $dateFrom->format('Y-m-d');
        }
        return $orderedByDateDatas;
    }
}
