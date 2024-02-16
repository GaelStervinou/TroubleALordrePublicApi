<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Invitation;
use App\Entity\Rate;
use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\UserRolesEnum;
use App\Enum\UserStatusEnum;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UsersPendingCompanyAdminValidationExtension implements QueryCollectionExtensionInterface
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

        if (User::USERS_PENDING_COMPANY_ADMIN_VALIDATION === $operation->getName()) {
            $rootAlias = $queryBuilder->getRootAliases()[ 0 ];
            $queryBuilder->andWhere(sprintf('%s.kbis IS NOT NULL AND JSON_GET_TEXT(%s.roles, 0) NOT LIKE :value ', $rootAlias, $rootAlias));
            $queryBuilder->setParameter('value', UserRolesEnum::COMPANY_ADMIN->value);
        }
    }
}