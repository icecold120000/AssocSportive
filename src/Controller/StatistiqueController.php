<?php

namespace App\Controller;

use App\Form\StatistiqueType;
use App\Repository\InscriptionRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class StatistiqueController extends AbstractController
{
    /**
     * @Route("/statistique", name="statistique")
     */
    public function index(InscriptionRepository $inscription,EvenementRepository $evenements): Response
    {

        $inscriptions = $inscription->findAll();
        $events = $evenements->findAll();

        return $this->render('statistique/index.html.twig', [
            'inscriptions' => $inscriptions,
            'events' => $events,
        ]);
    }
}