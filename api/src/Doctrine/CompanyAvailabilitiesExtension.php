<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Availability;
use App\Entity\Company;
use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class CompanyAvailabilitiesExtension implements QueryCollectionExtensionInterface
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

        if (Availability::COMPANY_AVAILABILITIES_ROUTE_NAME !== $operation->getName()) {
            return;
        }
        $companyId = $context[ 'request' ]?->get('id');
        /**@var $loggedInUser User*/
        $loggedInUser = $this->security->getUser();
        if (!$loggedInUser->getOwnedCompanies()->exists(function (int $index, Company $company) use ($companyId) {
            return $companyId === $company->getId()?->toString() && $company->isActive();
        })) {
            throw new HttpException(403, 'Vous ne possédez pas cette entreprise ou cette dernière n\'est plus active');
        }

        if (!$this->security->isGranted("ROLE_ADMIN")) {
            $rootAlias = $queryBuilder->getRootAliases()[ 0 ];
            $queryBuilder->andWhere(sprintf('%s.company = :searchedCompanyId', $rootAlias));
            $queryBuilder->setParameter('searchedCompanyId', $companyId);
        }
    }
}