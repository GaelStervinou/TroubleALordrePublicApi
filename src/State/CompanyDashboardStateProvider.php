<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\CompanyDashboard;
use App\Entity\Company;
use App\Entity\Rate;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\RateRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CompanyDashboardStateProvider implements ProviderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        private Security       $security
    )
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return [];
        }

        $companyDashboard = (new CompanyDashboard())->setId($uriVariables[ 'id' ]);
        /**
         * @var $reservationRepository ReservationRepository
         */
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);
        $todayDate = new \DateTimeImmutable();
        $companyId = $uriVariables[ 'id' ];
        if (!$this->security->getUser()->getOwnedCompanies()->exists(
                function (int $index, Company $company) use ($companyId) {
                    dump($company->getId()->toString(), $company->isActive());
                    return $companyId === $company->getId()->toString() && $company->isActive();
                })) {
            return [];
        }


        $previousMonthDateFrom = \DateTimeImmutable::createFromFormat('U', strtotime("-2 months", strtotime($todayDate->setTime(0, 0)->format('Y-m-d H:i:s'))));
        $previousMonthDateTo = \DateTimeImmutable::createFromFormat('U', strtotime("-1 month", strtotime($todayDate->setTime(23, 59)->format('Y-m-d H:i:s'))));

        $previousMonthReservations = $reservationRepository->getCompanyReservationsFromDateToDate(
            $previousMonthDateFrom,
            $previousMonthDateTo,
            $companyId
        );

        $companyDashboard->setNumberOfReservationsPreviousMonth(count($previousMonthReservations));
        $companyDashboard->setMonthSalesNumberPreviousMonth($this->calculateMonthSales($previousMonthReservations));

        $currentMonthDateFrom = \DateTimeImmutable::createFromFormat('U', strtotime("-1 month", strtotime($todayDate->setTime(0, 0)->format('Y-m-d H:i:s'))));

        $currentMonthReservations = $reservationRepository->getCompanyReservationsFromDateToDate(
            $currentMonthDateFrom,
            $todayDate,
            $uriVariables[ 'id' ]
        );
        $companyDashboard->setNumberOfReservationsCurrentMonth(count($currentMonthReservations));
        $formattedReservationsAndSalesAmountPreviousMonth = $this->associateDayOfMonthToReservationNumberAndSalesAmount($previousMonthReservations, $previousMonthDateFrom, $previousMonthDateTo, 0);
        $companyDashboard->setReservationsPreviousMonth($formattedReservationsAndSalesAmountPreviousMonth[ 'reservationsByDate' ]);
        $companyDashboard->setMonthSalesPreviousMonth($formattedReservationsAndSalesAmountPreviousMonth[ 'salesAmountByDate' ]);


        $companyDashboard->setMonthsSalesAmountCurrentMonth($this->calculateMonthSales($currentMonthReservations));
        $formattedReservationsAndSalesAmountCurrentMonth = $this->associateDayOfMonthToReservationNumberAndSalesAmount($currentMonthReservations, $currentMonthDateFrom, $todayDate, 0);
        $companyDashboard->setReservationsCurrentMonth($formattedReservationsAndSalesAmountCurrentMonth[ 'reservationsByDate' ]);
        $companyDashboard->setMonthSalesCurrentMonth($formattedReservationsAndSalesAmountCurrentMonth[ 'salesAmountByDate' ]);
        $bestTroubleMakerData = $reservationRepository->getCompanyBestTroubleMakerFromDateToDate(
            \DateTimeImmutable::createFromFormat('U', strtotime("-1 month", strtotime($todayDate->setTime(0, 0)->format('Y-m-d H:i:s')))),
            $todayDate,
            $uriVariables[ 'id' ]
        );
        if (count($bestTroubleMakerData) > 0) {
            $bestTroubleMaker = $this->entityManager->getRepository(User::class)->find($bestTroubleMakerData[ 'id' ]->serialize());
            if ($bestTroubleMaker) {
                $companyDashboard->setBestTroubleMaker($bestTroubleMaker->setCurrentMonthTotalReservations($bestTroubleMakerData[ 'best_trouble_maker' ]));
            }
        }



        /**
         * @var $rateRepository RateRepository
         */
        $rateRepository = $this->entityManager->getRepository(Rate::class);
        $companyDashboard->setAverageRateForPreviousMonth((float)$rateRepository->getRatesForCompanyReservationsFromDateToDate(
            $previousMonthDateFrom,
            $previousMonthDateTo,
            $uriVariables[ 'id' ]
        ));
        $companyDashboard->setAverageRateForCurrentMonth((float)$rateRepository->getRatesForCompanyReservationsFromDateToDate(
            $currentMonthDateFrom,
            $todayDate,
            $uriVariables[ 'id' ]
        ));
        //TODO CA par jour mois d'avant + nombre de resa du user
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
        for ($i = 0; $i <= $diffInDays; $i++) {
            if (!array_key_exists($dateFromToString, $datas)) {
                $orderedByDateDatas[ $dateFromToString ] = $value;
            } else {
                $orderedByDateDatas[ $dateFromToString ] = $datas[ $dateFromToString ];
            }
            $dateFrom = $dateFrom->add(new \DateInterval('P1D'));
            $dateFromToString = $dateFrom->format('Y-m-d');
        }
        return $orderedByDateDatas;
    }
}
