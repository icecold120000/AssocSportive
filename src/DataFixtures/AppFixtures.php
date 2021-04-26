<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Document;
use App\Entity\CategorieEleve;
use App\Entity\CategorieDocument;
use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Entity\TypeEvenement;
use App\Entity\Sport;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {

        /*Data fixtures des utilisateurs*/
        $roles = [
            User::ROLE_USER,
            User::ROLE_ADMIN,
            User::ROLE_ELEVE,
        ];


        $user = new User();
        $password = 'admin';
        
        $user->setRoles([$roles[1]]);
        $user->setEmail('jeff.martins1@gmail.com');
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $user->setNomUser("Jeff");
        $user->setPrenomUser("Martins");
        $manager->persist($user);
        $this->addReference('user_admin_1', $user);
        $manager->flush();

        for ($i=0; $i < 10 ; $i++) { 
            $user2 = new User();
            $password2 = 'eleve';
            
            $user2->setRoles([$roles[2]]);
            $user2->setEmail('Luigi.gonzales'.$i.'@gmail.com');
            $user2->setPassword($this->encoder->encodePassword($user2, $password2));
            $user2->setNomUser("Luigi");
            $user2->setPrenomUser("Gonzales");
            $manager->persist($user2);
            $this->addReference('user_'. $i, $user2);
            $manager->flush();
        }

        /*Data fixtures d'une classe*/

        $classe = new Classe();

        $classe->setLibelle("BTS 1");
        $manager->persist($classe);
        $this->addReference('classe_1', $classe);
        $manager->flush();

        /*Data fixtures d'une catégorie d'élève*/

        $categEleve = new CategorieEleve();

        $categEleve->setLibelleCategorie("Cadet");
        $manager->persist($categEleve);
        $this->addReference('categEleve_1', $categEleve);
        $manager->flush();

        /*Data fixtures d'un type d'événement*/

        $typeEvent = new TypeEvenement();

        $typeEvent->setNom("Caricative");
        $manager->persist($typeEvent);
        $this->addReference('typeEvent_1', $typeEvent);
        $manager->flush();

        /*Data fixtures d'une catégorie de document*/

        $categDoc = new CategorieDocument();

        $categDoc->setLibelleCategorieDoc("Administrative");
        $manager->persist($categDoc);
        $this->addReference('categDoc_1', $categDoc);
        $manager->flush();

        /*Data fixtures d'un sport*/

        $sport = new Sport();

        $sport->setNomSport("Boxe");
        $manager->persist($sport);
        $this->addReference('sport_1', $sport);
        $manager->flush();

        /*Data fixtures des élèves*/

        for ($i=0; $i < 10 ; $i++) {

            $user = $this->getReference('user_'.$i);

            $eleve = new Eleve();
            $eleve->setNomEleve('nom '.$i)
                ->setPrenomEleve('prenom '.$i)
                ->setDateNaissance(new \DateTime("12/10/2001"))
                ->setGenreEleve('H')
                ->setDateCreation(new \DateTime())
                ->setNumTelEleve(653098834)
                ->setNumTelParent(344668782)
                ->setArchiveEleve(0)
                ->setUtilisateur($user)
                ->setCategorie($this->getReference('categEleve_1'));
            $manager->persist($eleve);
            $this->addReference('eleve_'. $i, $eleve);
            $manager->flush();
        }

        /*Data fixtures d'un événement*/

        $event = new Evenement();

        $event->setNomEvenement("Imagine for Margot")
              ->setDateDebut(new \DateTime("03/03/2021 12:00:00"))
              ->setDateFin(new \DateTime("03/04/2021 12:30:00"))
              ->setLieuEvenement("Saint Dominique, Mortefontaine")
              ->setCoutEvenement(20)
              ->setDescripEvenement("Course pour l'association caricative Imagine for Margot")
              ->setNbPlace(40)
              ->setSport($this->getReference('sport_1'))
              ->setType($this->getReference('typeEvent_1'))
              ->setCategorieEleve($this->getReference('categEleve_1'));
        $manager->persist($event);
        $this->addReference('event_1', $event);
        $manager->flush();


        /*Data fixtures d'un document*/

        $document = new Document();

        $document->setNomDocument("Inscription Imagine for Margot")
                 ->setLienDocument("./uploads/document/Inscription.pdf")
                 ->setDescriptionDocument("Document permettant l'inscription")
                 ->setEvenement($this->getReference('event_1'))
                 ->setCategorieDocument($this->getReference('categDoc_1'))
                 ->setDateAjout(new \DateTime());
        $manager->persist($document);
        $this->addReference('document_1', $document);
        $manager->flush();

        /*Data fixtures d'une inscription*/

        $inscription = new Inscription();

        $inscription->setDateInscription(new \DateTime())
                    ->setEleve($this->getReference('eleve_1'))
                    ->setEvenement($this->getReference('event_1'));
        $manager->persist($inscription);
        $this->addReference('inscription_1', $inscription);
        $manager->flush(); 

    }
}
