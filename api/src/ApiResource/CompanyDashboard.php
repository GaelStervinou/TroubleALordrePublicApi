<?php

namespace App\ApiResource;


use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\User;
use App\State\CompanyDashboardStateProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/companies/{id}/dashboard',
            uriVariables: [
                'id'
            ],
            requirements: [
                'id' => '[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}',
            ],
            normalizationContext: ['groups' => ['company:dashboard:read']],
            name: 'company_dashboard',
            provider: CompanyDashboardStateProvider::class,
        )
    ]
)]
class CompanyDashboard
{
    #[ApiProperty(identifier: true)]
    #[Groups(['company:dashboard:read'])]
    private ?string $id = null;
    #[Groups(['company:dashboard:read'])]
    private array $reservationsCurrentMonth = [];
    #[Groups(['company:dashboard:read'])]
    private array $reservationsPreviousMonth = [];
    #[Groups(['company:dashboard:read'])]
    private int $numberOfReservationsCurrentMonth = 0;
    #[Groups(['company:dashboard:read'])]
    private int $numberOfReservationsPreviousMonth = 0;
    #[Groups(['company:dashboard:read'])]
    private array $monthSalesCurrentMonth = [];
    #[Groups(['company:dashboard:read'])]
    private array $monthSalesPreviousMonth = [];
    #[Groups(['company:dashboard:read'])]
    private float $monthsSalesAmountCurrentMonth = 0;
    #[Groups(['company:dashboard:read'])]
    private float $monthSalesNumberPreviousMonth = 0;
    #[Groups(['company:dashboard:read'])]
    private ?User $bestTroubleMaker = null;
    #[Groups(['company:dashboard:read'])]
    private float $averageRateForCurrentMonth = 0;
    #[Groups(['company:dashboard:read'])]
    private float $averageRateForPreviousMonth = 0;

    public function getReservationsCurrentMonth(): array
    {
        return $this->reservationsCurrentMonth;
    }

    public function setReservationsCurrentMonth(array $reservationsCurrentMonth): CompanyDashboard
    {
        $this->reservationsCurrentMonth = $reservationsCurrentMonth;
        return $this;
    }

    public function getReservationsPreviousMonth(): array
    {
        return $this->reservationsPreviousMonth;
    }

    public function setReservationsPreviousMonth(array $reservationsPreviousMonth): CompanyDashboard
    {
        $this->reservationsPreviousMonth = $reservationsPreviousMonth;
        return $this;
    }

    public function getNumberOfReservationsPreviousMonth(): int
    {
        return $this->numberOfReservationsPreviousMonth;
    }

    public function setNumberOfReservationsPreviousMonth(int $numberOfReservationsPreviousMonth): CompanyDashboard
    {
        $this->numberOfReservationsPreviousMonth = $numberOfReservationsPreviousMonth;
        return $this;
    }

    public function getMonthSalesCurrentMonth(): array
    {
        return $this->monthSalesCurrentMonth;
    }

    public function setMonthSalesCurrentMonth(array $monthSalesCurrentMonth): CompanyDashboard
    {
        $this->monthSalesCurrentMonth = $monthSalesCurrentMonth;
        return $this;
    }

    public function getMonthSalesPreviousMonth(): array
    {
        return $this->monthSalesPreviousMonth;
    }

    public function setMonthSalesPreviousMonth(array $monthSalesPreviousMonth): CompanyDashboard
    {
        $this->monthSalesPreviousMonth = $monthSalesPreviousMonth;
        return $this;
    }

    public function getMonthsSalesAmountCurrentMonth(): float
    {
        return $this->monthsSalesAmountCurrentMonth;
    }

    public function setMonthsSalesAmountCurrentMonth(float $monthsSalesAmountCurrentMonth): CompanyDashboard
    {
        $this->monthsSalesAmountCurrentMonth = $monthsSalesAmountCurrentMonth;
        return $this;
    }

    public function getMonthSalesNumberPreviousMonth(): float
    {
        return $this->monthSalesNumberPreviousMonth;
    }

    public function setMonthSalesNumberPreviousMonth(float $monthSalesNumberPreviousMonth): CompanyDashboard
    {
        $this->monthSalesNumberPreviousMonth = $monthSalesNumberPreviousMonth;
        return $this;
    }

    public function getBestTroubleMaker(): ?User
    {
        return $this->bestTroubleMaker;
    }

    public function setBestTroubleMaker(?User $bestTroubleMaker): CompanyDashboard
    {
        $this->bestTroubleMaker = $bestTroubleMaker;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getNumberOfReservationsCurrentMonth(): int
    {
        return $this->numberOfReservationsCurrentMonth;
    }

    public function setNumberOfReservationsCurrentMonth(int $numberOfReservationsCurrentMonth): self
    {
        $this->numberOfReservationsCurrentMonth = $numberOfReservationsCurrentMonth;
        return $this;
    }

    public function getAverageRateForCurrentMonth(): float
    {
        return $this->averageRateForCurrentMonth;
    }

    public function setAverageRateForCurrentMonth(float $averageRateForCurrentMonth): CompanyDashboard
    {
        $this->averageRateForCurrentMonth = $averageRateForCurrentMonth;
        return $this;
    }

    public function getAverageRateForPreviousMonth(): float
    {
        return $this->averageRateForPreviousMonth;
    }

    public function setAverageRateForPreviousMonth(float $averageRateForPreviousMonth): CompanyDashboard
    {
        $this->averageRateForPreviousMonth = $averageRateForPreviousMonth;
        return $this;
    }
}