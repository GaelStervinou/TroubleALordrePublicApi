<?php

namespace App\Repository;

use App\Entity\Rate;
use App\Entity\Reservation;
use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rate>
 *
 * @method Rate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rate[]    findAll()
 * @method Rate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function getRatesForCompanyReservationsFromDateToDate(
        \DateTimeImmutable $dateFrom,
        \DateTimeImmutable $dateTo,
        string             $companyId
    ) {
        $query = $this->createQueryBuilder('r')
            ->select('AVG(r.value)')
            ->leftJoin(Service::class, 's', Join::WITH, 'r.service = s.id')
            ->leftJoin(Reservation::class, 'res', Join::WITH, 'r.reservation = res.id')
            ->where('res.date BETWEEN :dateFrom AND :dateTo')
            ->andWhere('s.company = :companyId')
            ->setParameter('companyId', $companyId, ParameterType::STRING)
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
        ;

        return $query->getQuery()->getSingleScalarResult();
    }
}
