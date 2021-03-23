<?php

namespace App\Repository;

use App\Entity\CategorieEleve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorieEleve|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieEleve|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieEleve[]    findAll()
 * @method CategorieEleve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieEleveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieEleve::class);
    }

    public function findOneByLibelleCat($value): ?CategorieEleve
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.libelleCategorie = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return CategorieEleve[] Returns an array of CategorieEleve objects
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
    public function findOneBySomeField($value): ?CategorieEleve
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
