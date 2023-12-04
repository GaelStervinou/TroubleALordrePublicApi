<?php

namespace App\Repository;

use App\Entity\Availibility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Availibility::class);
    }

//    /**
//     * @return Availibility[] Returns an array of Availibility objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Availibility
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
