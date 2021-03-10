<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/admin", name="profile_admin", methods={"GET"})
     */
    public function admin(UserRepository $user): Response
    {
        return $this->render('profile/admin.html.twig', [
        	'user' => $user->findAll(),	
    	]);
    }

    /**
     * @Route("/profile/eleve", name="profile_eleve", methods={"GET"})
     */
    public function eleve(UserRepository $user, EvenementRepository $evenements): Response
    {

        return $this->render('profile/eleve.html.twig', [
        	'user' => $user->findAll(),
            'events' => $evenements->findAll(),
    	]);
    }

    /**
     * @Route("/profile/tiers", name="profile_tiers", methods={"GET"})
     */
    public function tiers(UserRepository $user): Response
    {
        return $this->render('profile/tiers.html.twig', [
        	'user' => $user->findAll(),	
    	]);
    }

     /**
     * @Route("/profile/enseignant", name="profile_enseignant", methods={"GET"})
     */
    public function enseignant(UserRepository $user): Response
    {
        return $this->render('profile/enseignant.html.twig', [
        	'user' => $user->findAll(),	
    	]);
    }
}
