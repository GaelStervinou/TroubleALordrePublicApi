<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Company;
use App\Entity\Rate;
use App\Entity\Reservation;
use App\Enum\CompanyStatusEnum;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CompanySearchExtension implements QueryCollectionExtensionInterface
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
        if (null === $operation || Company::COMPANY_SEARCH_ROUTE !== $operation->getName()) {
            return;
        }

        $lat = $context[ 'request' ]?->get('lat');
        $lng = $context[ 'request' ]?->get('lng');
        //TODO check validity by regex
        $rootAlias = $queryBuilder->getRootAliases()[ 0 ];

        $queryBuilder->andWhere(sprintf('%s.status = :status', $rootAlias))
            ->setParameter('status', CompanyStatusEnum::ACTIVE->value);

        $queryBuilder->andWhere(sprintf(
            "ST_DWithin(
            ST_MakePoint(%s.lng, %s.lat),
            ST_MakePoint(:lng, :lat),
          5000) = TRUE", $rootAlias, $rootAlias
        ))
            ->setParameter('lat', $lat, ParameterType::STRING)
            ->setParameter('lng', $lng, ParameterType::STRING);
    }
}