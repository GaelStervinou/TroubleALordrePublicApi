<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Availibility;
use App\Entity\Unavailibility;
use App\Entity\User;
use App\Repository\AvailibilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TroubleMakerPlanningStateProvider implements ProviderInterface
{
    private EntityManagerInterface $entityManager;
    public function __construct(
        EntityManagerInterface $entityManager,
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private Pagination $pagination
    )
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        dd( $this->pagination->getOffset($operation, $context));
        if (!($operation instanceof CollectionOperationInterface)) {
            return null;
        }
        //TODO récupérer dispo entreprise + dispo perso et enlever indispo + resa existantes


        $user = $this->entityManager->getRepository(User::class)->find($uriVariables['userId']);
        if(!$user) {
            return null;
        }
        $offset = $this->pagination->getOffset($operation, $context);
        /**
         * @var $availibilityRepository AvailibilityRepository
         */
        $availibilityRepository = $this->entityManager->getRepository(Availibility::class);
        $unavailabilitiesRepository = $this->entityManager->getRepository(Unavailibility::class);
        $reservationRepository = $this->entityManager->getRepository(Availibility::class);

        $userAvailabilities = $availibilityRepository->getTroubleMakerAvailabilityFromDateToDate($user->getId(), $user->getCompany()?->getId(), $offset, $page);
        dd($userAvailabilities);
        if (0 === $userAvailabilities->count()) {
            //TODO changer null par renvoyer trsversablepaginator vide pareille pour les autres return null
            return null;
        }
        $userUnavailibilities = $unavailabilitiesRepository->findBy(['user_id' => $user->getId()]);
        $reservations = $reservationRepository->findBy(['user_id' => $user->getId()]);

        //TODO tout classer par jour dans les indispo et pareil pour les dispo

    }
}
