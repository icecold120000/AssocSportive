<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(EvenementRepository $evenementRepo,
     Request $request, PaginatorInterface $paginator): Response
    {

        $evenement = $evenementRepo->findAll();

        $evenements = $paginator->paginate(
            $evenement,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('homepage/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }
}
