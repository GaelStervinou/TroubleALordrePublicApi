<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Company;
use App\Entity\Rate;
use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\CompanyStatusEnum;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

final readonly class OnlyActiveCompaniesExtension implements QueryCollectionExtensionInterface
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
        if (null === $operation || Company::COMPANIES_ROUTE_NAME !== $operation->getName()) {
            return;
        }
        if ($this->security->isGranted("ROLE_ADMIN") ) {
            $rootAlias = $queryBuilder->getRootAliases()[ 0 ];
            $queryBuilder->andWhere(sprintf('%s.status = :status', $rootAlias))
                ->setParameter('status', CompanyStatusEnum::ACTIVE->value);
        }
    }
}