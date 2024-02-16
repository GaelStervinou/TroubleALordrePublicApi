<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Rate;
use App\Entity\Reservation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UserReservationsExtension implements QueryCollectionExtensionInterface
{

    public function __construct(
        private Security $security,
    )
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $operation, $context);
    }


    private function addWhere(QueryBuilder $queryBuilder, ?Operation $operation = null, array $context = []): void
    {
        if (null === $operation) {
            return;
        }

        if (Reservation::USER_RESERVATIONS_AS_CUSTOMERS === $operation->getName()) {
            $userId = $context[ 'request' ]?->get('id');
            if (!$userId) {
                return;
            }
            $rootAlias = $queryBuilder->getRootAliases()[ 0 ];
            $queryBuilder->andWhere(sprintf('%s.customer = :searchedUserId', $rootAlias));
            $queryBuilder->setParameter('searchedUserId', $userId);
        } elseif (Reservation::USER_RESERVATIONS_AS_TROUBLE_MAKERS === $operation->getName()) {
            $userId = $context[ 'request' ]?->get('id');
            if (!$userId) {
                return;
            }
            $rootAlias = $queryBuilder->getRootAliases()[ 0 ];
            $queryBuilder->andWhere(sprintf('%s.troubleMaker = :searchedUserId', $rootAlias));
            $queryBuilder->setParameter('searchedUserId', $userId);
        }
    }
}