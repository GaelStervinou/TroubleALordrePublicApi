<?php

namespace App\Repository;

use App\Entity\Availibility;
use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Availibility>
 *
 * @method Availibility|null find($id, $lockMode = null, $lockVersion = null)
 * @method Availibility|null findOneBy(array $criteria, array $orderBy = null)
 * @method Availibility[]    findAll()
 * @method Availibility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvailibilityRepository extends ServiceEntityRepository
{
    public const DEFAULT_PAGINATION_LIMIT = 7;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Availibility::class);
    }

    public function getTroubleMakerAvailabilityFromDateToDate(
        string $userId,
        string $companyId,
        int $limit = self::DEFAULT_PAGINATION_LIMIT,
        int $page = 1
    ): Collection
    {
        if (1 > $page) {
            $page = 1;
        }
        $dateFrom = (new \DateTimeImmutable())->add(new \DateInterval("P+{$offset}D"));

         $query = $this->createQueryBuilder('a')
            ->select()
            ->where('a.user_id = :userId')
            ->orWhere('a.company_id = :companyId')
            ->setParameter('userId', $userId, ParameterType::STRING)
            ->setParameter('companyId', $companyId, ParameterType::STRING)
        ;

        return $query->getQuery()->execute();
    }
}
