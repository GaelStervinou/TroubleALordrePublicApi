<?php

namespace App\Repository;

use App\Entity\Unavailibility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Unavailibility>
 *
 * @method Unavailibility|null find($id, $lockMode = null, $lockVersion = null)
 * @method Unavailibility|null findOneBy(array $criteria, array $orderBy = null)
 * @method Unavailibility[]    findAll()
 * @method Unavailibility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnavailibilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unavailibility::class);
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
