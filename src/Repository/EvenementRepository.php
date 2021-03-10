<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('dateDebut' => 'DESC'));
    }

    /**
     * @return void
     */
    public function search($typeEvent = null, $categorie = null, $sport = null, $actif = null){
        $query = $this->createQueryBuilder('ev');
        if($typeEvent != null){
            $query->leftJoin('ev.Type', 'te');
            $query->andWhere('te.id = :id')
                ->setParameter('id', $typeEvent);
        }
        if($categorie != null){
            $query->leftJoin('ev.categorieEleve', 'ce');
            $query->andWhere('ce.id = :id')
                ->setParameter('id', $categorie);
        }
        if($sport != null){
            $query->leftJoin('ev.Sport', 'sp');
            $query->andWhere('sp.id = :id')
                ->setParameter('id', $sport);
        }
        if($actif != null){
            $date = new \DateTime('now');
            if($actif === true){
                $query->andWhere(':date Between
                 ev.dateDebut And ev.dateFin')
                    ->setParameter('date', $date);
            }
        }
        return $query->getQuery()->getResult();
    }
    


    // /**
    //  * @return Evenement[] Returns an array of Evenement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Evenement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
