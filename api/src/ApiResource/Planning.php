<?php

namespace App\ApiResource;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\TroubleMakerPlanningStateProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/plannings/{userId}/{serviceId}',
            normalizationContext: ['groups' => ['planning:read']],
            provider: TroubleMakerPlanningStateProvider::class,
        )
    ],
    paginationItemsPerPage: 7
)]
class Planning
{
    private ?string $date = null;
    private array $shifts = [];

    #[Groups(['planning:read'])]
    public function getDate(): ?string
    {
        return $this->date;
    }

    #[Groups(['planning:read'])]
    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getShifts(): array
    {
        return $this->shifts;
    }

    public function setShifts(array $shifts): self
    {
        $this->shifts = $shifts;

        return $this;
    }
}