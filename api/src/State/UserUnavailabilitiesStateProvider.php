<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\ApiResource\Planning;
use App\Entity\Unavailability;
use App\Entity\User;
use App\Repository\UnavailabilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UserUnavailabilitiesStateProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface                                                    $entityManager,
        private Pagination                                                        $pagination,
        private Security                                                          $security
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            /***@var $user User */
            $user = $this->entityManager->getRepository(User::class)->find($uriVariables[ 'id' ]);

            if (
                !$user->isTroubleMaker()
                || $this->security->getUser() !== $user->getCompany()->getOwner()
            ) {
                throw new ValidationException("Utilisateur introuvable");
            }

            /**
             * @var $availibilityRepository UnavailabilityRepository
             */
            $availibilityRepository = $this->entityManager->getRepository(Unavailability::class);

            $offset = $this->pagination->getOffset($operation, $context);
            $dateFrom = (new \DateTimeImmutable())->setTime(0, 0)->add(new \DateInterval("P{$offset}D"));
            if (0 !== $offset) {
                $dateFrom = (new \DateTimeImmutable())->add(new \DateInterval("P{$offset}D"));
            }
            $dateTo = $dateFrom->add(new \DateInterval("P7D"));

            $userUnavailabilities = $availibilityRepository->getTroubleMakerUnavailabilityFromDateToDate($user->getId(), $dateFrom, $dateTo);
            if (0 === count($userUnavailabilities)) {
                return [];
            }

            $formattedUnavailabilities = $this->formatUnavailabilities($userUnavailabilities);
            $planningDays = [];
            foreach ($formattedUnavailabilities as $day => $unavailability) {
                $planning = (new Planning())
                    ->setDate($day)
                    ->setShifts($unavailability)
                    ->formatThisShiftsFromTimestampToString()
                ;
                $planningDays[] = $planning;
            }

            return $planningDays;
        }
        return [null];
    }

    private function formatUnavailabilities(array $userUnavailabilities): array
    {
        $unavailabilities = [];
        /**
         * @var $unavailability Unavailability
         */
        foreach ($userUnavailabilities as $unavailability) {
            $startTime = $unavailability->getStartTime();
            $unavailabilities[ $startTime?->format('Y-m-d') ][] = [
                '@id' => $unavailability->getId()->toString(),
                'startTime' => strtotime($startTime?->format('Y-m-d H:i:s')),
                'endTime' => strtotime($unavailability->getEndTime()?->format('Y-m-d H:i:s'))
            ];
        }

        return $unavailabilities;
    }

}
