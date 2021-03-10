<?php

namespace App\Repository;

use App\Entity\Eleve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Eleve|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eleve|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eleve[]    findAll()
 * @method Eleve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EleveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eleve::class);
    }

    /**
     * @return void
     */
    public function search($classe = null, $genre = null, $archive = null){
        $query = $this->createQueryBuilder('el');
        if($classe != null){
            $query->leftJoin('el.Classe', 'cl');
            $query->andWhere('cl.id = :id')
                ->setParameter('id', $classe);
        }
        if($genre != null){
            $query->andWhere('el.genreEleve LIKE :genre')
                ->setParameter('genre', $genre);
        }
        if($archive != null){
            $query->andWhere('el.archiveEleve LIKE :archive')
                ->setParameter('archive', $archive);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @return void
     */
    public function searchEleve($nom = null, $classe = null, $genre = null){
        $query = $this->createQueryBuilder('el');
        if($nom != null){
            $query->andWhere('el.nomEleve LIKE :nom OR el.prenomEleve LIKE :nom
                OR el.id LIKE :nom')
                ->setParameter('nom', $nom);
        }
        if($classe != null){
            $query->leftJoin('el.Classe', 'cl');
            $query->andWhere('cl.id = :id')
                ->setParameter('id', $classe);
        }
        if($genre != null){
            $query->andWhere('el.genreEleve LIKE :genre')
                ->setParameter('genre', $genre);
        }
        return $query->getQuery()->getResult();
    }

    public function findOneEleveBy($nom, $prenom, $naissance): ?Eleve
    {
        return $this->createQueryBuilder('e')
            ->Where('e.nomEleve = :val')
            ->andWhere('e.prenomEleve = :val2')
            ->andWhere('e.dateNaissance = :val3')
            ->setParameters(array('val' => $nom, 'val2' => $prenom, 'val3' => $naissance),
             array("string", "string","\DateTime"))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return Eleve[] Returns an array of Eleve objects
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
    public function findOneBySomeField($value): ?Eleve
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
