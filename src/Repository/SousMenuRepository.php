<?php

namespace App\Repository;

use App\Entity\SousMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SousMenu>
 *
 * @method SousMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method SousMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method SousMenu[]    findAll()
 * @method SousMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousMenu::class);
    }

//    /**
//     * @return SousMenu[] Returns an array of SousMenu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SousMenu
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
