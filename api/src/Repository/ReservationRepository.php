<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Service;
use App\Enum\ReservationStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }
    public function getTroubleMakerReservationsFromDateToDate(
        string $userId,
        \DateTimeImmutable $dateFrom,
        \DateTimeImmutable $dateTo
    ): array
    {
        $query = $this->createQueryBuilder('r')
            ->select()
            ->where('(r.troubleMaker = :userId AND r.date BETWEEN :dateFrom AND :dateTo)')
            ->andWhere('r.status IN (:status)')
            ->setParameter('userId', $userId, ParameterType::STRING)
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->setParameter('status', [ReservationStatusEnum::PENDING, ReservationStatusEnum::ACTIVE])
        ;

        return $query->getQuery()->execute();
    }

    public function getCompanyReservationsFromDateToDate(
        \DateTimeImmutable $dateFrom,
        \DateTimeImmutable $dateTo,
        string $companyId
    ): array
    {
        $query = $this->createQueryBuilder('r')
            ->select()
            ->leftJoin(Service::class, 's', Join::WITH, 'r.service = s.id')
            ->where('s.company = :companyId AND r.date BETWEEN :dateFrom AND :dateTo AND r.status = :status')
            ->setParameter('companyId', $companyId, ParameterType::STRING)
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->setParameter('status', ReservationStatusEnum::FINISHED->value, ParameterType::STRING)
        ;

        return $query->getQuery()->execute();
    }

    public function getCompanyBestTroubleMakerFromDateToDate(
        \DateTimeImmutable $dateFrom,
        \DateTimeImmutable $dateTo,
        string $companyId
    ): string
    {
        $query = $this->createQueryBuilder('r')
            ->select('COUNT(r.troubleMaker) AS BEST, MAX(BEST) AS BEST_TRUC')
            ->leftJoin(Service::class, 's', Join::WITH, 'r.service = s.id')
            ->where('s.company = :companyId AND r.date BETWEEN :dateFrom AND :dateTo AND r.status = :status')
            ->setParameter('companyId', $companyId, ParameterType::STRING)
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->setParameter('status', ReservationStatusEnum::FINISHED->value, ParameterType::STRING)
        ;

        return $query->getQuery()->execute();
    }
}
