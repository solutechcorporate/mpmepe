<?php

namespace App\Repository;

use App\Entity\ValeurDemande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ValeurDemande>
 *
 * @method ValeurDemande|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValeurDemande|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValeurDemande[]    findAll()
 * @method ValeurDemande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValeurDemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValeurDemande::class);
    }

//    /**
//     * @return ValeurDemande[] Returns an array of ValeurDemande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ValeurDemande
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
