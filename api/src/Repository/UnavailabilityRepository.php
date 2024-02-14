<?php

namespace App\Repository;

use App\Entity\Unavailability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Unavailability>
 *
 * @method Unavailability|null find($id, $lockMode = null, $lockVersion = null)
 * @method Unavailability|null findOneBy(array $criteria, array $orderBy = null)
 * @method Unavailability[]    findAll()
 * @method Unavailability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnavailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unavailability::class);
    }

    public function getTroubleMakerUnavailabilityFromDateToDate(
        string $userId,
        \DateTimeImmutable $dateFrom,
        \DateTimeImmutable $dateTo
    ): array
    {
        $query = $this->createQueryBuilder('u')
            ->select()
            ->where('(u.troubleMaker = :userId AND u.startTime BETWEEN :dateFrom AND :dateTo)')
            ->setParameter('userId', $userId, ParameterType::STRING)
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo);

        return $query->getQuery()->execute();
    }
}
