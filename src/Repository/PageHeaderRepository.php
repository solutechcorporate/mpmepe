<?php

namespace App\Repository;

use App\Entity\PageHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageHeader>
 *
 * @method PageHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageHeader[]    findAll()
 * @method PageHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageHeader::class);
    }

//    /**
//     * @return PageHeader[] Returns an array of PageHeader objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PageHeader
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
