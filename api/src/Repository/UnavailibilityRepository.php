<?php

namespace App\Repository;

use App\Entity\Unavailibility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

//    /**
//     * @return Unavailibility[] Returns an array of Unavailibility objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Unavailibility
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
