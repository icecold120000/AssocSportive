<?php

namespace App\DataFixtures;

use App\Entity\Eleve;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
    	for ($i=0; $i < 10 ; $i++) { 

    		$eleve = new Eleve();
    		$eleve = setNom('nom '.$i);
    		$eleve = setPrenom('prenom '.$i);
    		$eleve = setDateNaissance(12,10,2000);
    		$eleve = setGenre('H');
    		$eleve = setDateCreation(new \DateTime());
    		$eleve = setArchive(0);
         	$manager->persist($eleve);
    	}


        $manager->flush();
    }
}
