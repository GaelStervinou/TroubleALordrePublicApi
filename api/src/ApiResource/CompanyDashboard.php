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
    ],
    paginationItemsPerPage: 7
)]
class CompanyDashboard
{
    #[ApiProperty(identifier: true)]
    #[Groups(['company:dashboard:read'])]
    private ?string $id = null;
    #[Groups(['company:dashboard:read'])]
    private array $reservationsCurrentMonth = [];
    #[Groups(['company:dashboard:read'])]
    private int $numberOfReservationsPreviousMonth = 0;
    #[Groups(['company:dashboard:read'])]
    private array $monthSalesCurrentMonth = [];
    #[Groups(['company:dashboard:read'])]
    private float $monthsSalesAmountCurrentMonth = 0;
    #[Groups(['company:dashboard:read'])]
    private float $monthSalesNumberPreviousMonth = 0;
    #[Groups(['company:dashboard:read'])]
    private ?User $bestTroubleMaker = null;

    public function getReservationsCurrentMonth(): array
    {
        return $this->reservationsCurrentMonth;
    }

    public function setReservationsCurrentMonth(array $reservationsCurrentMonth): CompanyDashboard
    {
        $this->reservationsCurrentMonth = $reservationsCurrentMonth;
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
}