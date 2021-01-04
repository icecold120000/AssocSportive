<?php

namespace App\Repository;

use App\Entity\CategorieDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorieDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieDocument[]    findAll()
 * @method CategorieDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieDocument::class);
    }

    // /**
    //  * @return CategorieDocument[] Returns an array of CategorieDocument objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategorieDocument
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
