<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\CompanyDashboard;
use App\Entity\Reservation;
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
        if (!($operation instanceof CollectionOperationInterface)) {
            return [];
        }

        $companyDashboard = new CompanyDashboard();
        /**
         * @var $reservationRepository ReservationRepository
         */
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);
        $todayDate = new \DateTimeImmutable();


        $previousMonthReservations = $reservationRepository->getCompanyReservationsFromDateToDate(
            \DateTimeImmutable::createFromFormat('U', strtotime("-2 months", strtotime($todayDate->setTime(0, 0)->format('Y-m-d H:i:s')))),
            \DateTimeImmutable::createFromFormat('U', strtotime("-1 month", strtotime($todayDate->setTime(23, 59)->format('Y-m-d H:i:s')))),
            $uriVariables[ 'companyId' ]
        );

        $companyDashboard->setNumberOfReservationsPreviousMonth(count($previousMonthReservations));
        $companyDashboard->setMonthSalesNumberPreviousMonth($this->calculateMonthSales($previousMonthReservations));

        $currentMonthReservations = $reservationRepository->getCompanyReservationsFromDateToDate(
            \DateTimeImmutable::createFromFormat('U', strtotime("-1 month", strtotime($todayDate->setTime(0, 0)->format('Y-m-d H:i:s')))),
            $todayDate,
            $uriVariables[ 'companyId' ]
        );

        $companyDashboard->setMonthsSalesAmountCurrentMonth($this->calculateMonthSales($currentMonthReservations));
        $formattedReservationsAndSalesAmountCurrentMonth = $this->associateDayOfMonthToReservationNumberAndSalesAmount($currentMonthReservations);
        $companyDashboard->setReservationsCurrentMonth($formattedReservationsAndSalesAmountCurrentMonth['reservationsByDate']);
        $companyDashboard->setMonthSalesCurrentMonth($formattedReservationsAndSalesAmountCurrentMonth['salesAmountByDate']);
        /*$bestTroubleMaker = $reservationRepository->getCompanyBestTroubleMakerFromDateToDate(
            \DateTimeImmutable::createFromFormat('U', strtotime("-1 month", strtotime($todayDate->setTime(0, 0)->format('Y-m-d H:i:s')))),
            $todayDate,
            $uriVariables[ 'companyId' ]
        );
        dd($bestTroubleMaker);*/
        return $companyDashboard;
    }

    private function calculateMonthSales(array $reservations): float
    {
        return array_reduce($reservations, static function ($amount, $reservation) {
            return $amount + $reservation->getPrice();
        },0);
    }

    private function associateDayOfMonthToReservationNumberAndSalesAmount(array $reservations): array
    {
        //TODO remplir les jours qui sont vides
        $finalArrayNumberOfReservations = [];
        $finalArraySalesAmount = [];
        /**
         * @var $reservation Reservation
         */
        foreach ($reservations as $reservation) {
            $date = $reservation->getDate()?->format('Y-m-d');
            if (array_key_exists($date, $finalArrayNumberOfReservations)) {
                $finalArrayNumberOfReservations[$date]++;
            } else {
                $finalArrayNumberOfReservations[$date] = 1;
            }

            if (array_key_exists($date, $finalArraySalesAmount)) {
                $finalArraySalesAmount[$date] += $reservation->getPrice();
            } else {
                $finalArraySalesAmount[$date] = $reservation->getPrice();
            }
        }
        return [
            'reservationsByDate' => $finalArrayNumberOfReservations,
            'salesAmountByDate' => $finalArraySalesAmount
        ];
    }
}
