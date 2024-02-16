<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Company;
use App\Enum\CompanyStatusEnum;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final readonly class CompanySearchExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $operation, $context);
    }


    private function addWhere(QueryBuilder $queryBuilder, ?Operation $operation = null, array $context = []): void
    {
        if (null === $operation || Company::COMPANY_SEARCH_ROUTE_NAME !== $operation->getName()) {
            return;
        }

        $lat = $context[ 'request' ]?->get('lat');
        $lng = $context[ 'request' ]?->get('lng');

        $rootAlias = $queryBuilder->getRootAliases()[ 0 ];

        $queryBuilder->andWhere(sprintf('%s.status = :status', $rootAlias))
            ->setParameter('status', CompanyStatusEnum::ACTIVE->value);

        $queryBuilder->andWhere(sprintf(
            "ST_DWithin(
            ST_MakePoint(%s.lng, %s.lat),
            ST_MakePoint(:lng, :lat),
          2.5) = TRUE", $rootAlias, $rootAlias
        ))
            ->setParameter('lat', $lat, ParameterType::STRING)
            ->setParameter('lng', $lng, ParameterType::STRING);
    }
}