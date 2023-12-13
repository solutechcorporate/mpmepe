<?php

namespace App\Repository;

use App\Entity\Copyright;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Copyright>
 *
 * @method Copyright|null find($id, $lockMode = null, $lockVersion = null)
 * @method Copyright|null findOneBy(array $criteria, array $orderBy = null)
 * @method Copyright[]    findAll()
 * @method Copyright[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CopyrightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Copyright::class);
    }

//    /**
//     * @return Copyright[] Returns an array of Copyright objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Copyright
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
