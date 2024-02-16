<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Planning;
use App\Entity\Availability;
use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\Unavailability;
use App\Entity\User;
use App\Repository\AvailabilityRepository;
use App\Repository\ReservationRepository;
use App\Repository\UnavailabilityRepository;
use App\Service\TroubleMakerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TroubleMakerPlanningStateProvider implements ProviderInterface
{

    public function __construct(
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private Pagination                                                        $pagination,
        private TroubleMakerService                                               $troubleMakerService
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return $this->troubleMakerService->getTroubleMakerPlanning($uriVariables[ 'userId' ], $uriVariables[ 'serviceId' ], $this->pagination->getOffset($operation, $context));
        }
    }
}
