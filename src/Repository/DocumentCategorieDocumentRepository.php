<?php

namespace App\Repository;

use App\Entity\DocumentCategorieDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentCategorieDocument>
 *
 * @method DocumentCategorieDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentCategorieDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentCategorieDocument[]    findAll()
 * @method DocumentCategorieDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentCategorieDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentCategorieDocument::class);
    }

//    /**
//     * @return DocumentCategorieDocument[] Returns an array of DocumentCategorieDocument objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DocumentCategorieDocument
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
