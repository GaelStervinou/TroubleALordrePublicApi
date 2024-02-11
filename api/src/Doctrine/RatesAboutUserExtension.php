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

final readonly class RatesAboutUserExtension implements QueryCollectionExtensionInterface
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
        $userId = $context['request']?->get('id');
        if (!$userId) {
            return;
        }
        if (Rate::USER_RATES_AS_CUSTOMER_OPERATION_NAME === $operation->getName()) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->orWhere(sprintf('%s.createdBy <> :searchedUserId AND %s.customer = :searchedUserId', $rootAlias, $rootAlias));
            $queryBuilder->setParameter('searchedUserId',$userId );
        } elseif (Rate::USER_RATES_AS_TROUBLE_MAKER_OPERATION_NAME === $operation->getName()) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->leftJoin(Reservation::class, 'r', Join::WITH, sprintf('%s.reservation = r.id', $rootAlias));
            $queryBuilder->orWhere(sprintf('%s.createdBy <> :searchedUserId AND r.troubleMaker = :searchedUserId', $rootAlias));
            $queryBuilder->setParameter('searchedUserId',$userId );
        }
    }
}