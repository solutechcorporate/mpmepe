<?php

namespace App\Repository;

use App\Entity\ContactValeurDemande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactValeurDemande>
 *
 * @method ContactValeurDemande|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactValeurDemande|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactValeurDemande[]    findAll()
 * @method ContactValeurDemande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactValeurDemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactValeurDemande::class);
    }

//    /**
//     * @return ContactValeurDemande[] Returns an array of ContactValeurDemande objects
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

//    public function findOneBySomeField($value): ?ContactValeurDemande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
