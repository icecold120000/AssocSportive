<?php

namespace App\Controller;
use App\Entity\Flux;
use App\Form\StatistiqueType;
use App\Repository\EleveRepository;
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
    public function index(EleveRepository $eleve,EvenementRepository $evenements,
     PaginatorInterface $paginator,Request $request): Response
    {

        $eleves = $eleve->findAll();
        $events = $evenements->findAll();
        $eventsFilter = $evenements->findAll();
        $eleveNbEvent = $eleve->findAll();

        $form = $this->createForm(StatistiqueType::class);
        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eleves = $eleve->findByAnneeNaissance(
                $search->get('Annee_de_Naissance')->getData()
            );

            $eventsFilter = $evenements->find(
                $search->get('EvenementFilter')->getData()
            );

            $eleveNbEvent = $eleve->find(
                $search->get('EleveFiltre')->getData()
            );
        }

        $eleves = $paginator->paginate(
            $eleves,
            $request->query->getInt('page',1),
            25
        );

        return $this->render('statistique/index.html.twig', [
            'eleves' => $eleves,
            'events' => $events,
            'eventsFilter' => $eventsFilter,
            'eleveNbEvent' => $eleveNbEvent,
            'fluxes' => $this->getDoctrine()->getRepository(Flux::class)->findAll(),
            'form' => $form->createView(),
        ]);
    }
}
