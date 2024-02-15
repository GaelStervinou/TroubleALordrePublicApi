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
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UsersInvitationsExtension implements QueryCollectionExtensionInterface
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

        if (Invitation::MY_INVITATIONS_ROUTE_NAME !== $operation->getName()) {
            return;
        }

        if (!$this->security->isGranted("ROLE_ADMIN")) {
            $rootAlias = $queryBuilder->getRootAliases()[ 0 ];
            $queryBuilder->andWhere(sprintf('%s.receiver = :searchedUserId', $rootAlias));
            $queryBuilder->setParameter('searchedUserId', $this->security->getUser()->getId());
        }
    }
}