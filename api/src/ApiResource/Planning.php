<?php

namespace App\ApiResource;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\State\TroubleMakerPlanningStateProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/plannings/{userId}/{serviceId}',
            requirements: [
                'userId' => '[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}',
                'serviceId' => '[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}',
                ],
            normalizationContext: ['groups' => ['planning:read']],
            name: 'planning',
            provider: TroubleMakerPlanningStateProvider::class,
        )
    ],
    paginationItemsPerPage: 7
)]
class Planning
{
    private ?\DateTimeImmutable $date = null;
    private array $shifts = [];

    #[Groups(['planning:read'])]
    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    #[Groups(['planning:read'])]
    public function setDate(?\DateTimeImmutable $date): self
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