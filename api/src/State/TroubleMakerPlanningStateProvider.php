<?php

namespace App\State;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Availibility;
use Doctrine\ORM\EntityManagerInterface;

class TroubleMakerPlanningStateProvider implements ProviderInterface
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!($operation instanceof Get)) {
            return null;
        }
        //TODO récupérer dispo entreprise + dispo perso et enlever indispo + resa existantes

        $availibilityRepository = $this->entityManager->getRepository(Availibility::class);
        $reservationRepository = $this->entityManager->getRepository(Availibility::class);

        return null;
    }
}
