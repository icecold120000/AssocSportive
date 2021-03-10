<?php

namespace App\Controller;

use App\Entity\CategorieEleve;
use App\Form\CategorieEleveType;
use App\Repository\CategorieEleveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorie/eleve")
 */
class CategorieEleveController extends AbstractController
{
    /**
     * @Route("/", name="categorie_eleve_index", methods={"GET"})
     */
    public function index(CategorieEleveRepository $categorieEleveRepository): Response
    {
        return $this->render('categorie_eleve/index.html.twig', [
            'categorie_eleves' => $categorieEleveRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="categorie_eleve_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $categorieEleve = new CategorieEleve();
        $form = $this->createForm(CategorieEleveType::class, $categorieEleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorieEleve);
            $entityManager->flush();

            $this->addFlash(
                'SuccessCategEleve',
                'Votre catégorie d\'élève a été sauvegardée'
            );

            return $this->redirectToRoute('categorie_eleve_new');
        }

        return $this->render('categorie_eleve/new.html.twig', [
            'categorie_eleve' => $categorieEleve,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_eleve_show", methods={"GET"})
     */
    public function show(CategorieEleve $categorieEleve): Response
    {
        return $this->render('categorie_eleve/show.html.twig', [
            'categorie_eleve' => $categorieEleve,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categorie_eleve_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CategorieEleve $categorieEleve): Response
    {
        $form = $this->createForm(CategorieEleveType::class, $categorieEleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'SuccessCategEleve',
                'Votre catégorie d\'élève a été modifiée'
            );

            return $this->redirectToRoute('categorie_eleve_edit', array('id' => $categorieEleve->getId()));
        }

        return $this->render('categorie_eleve/edit.html.twig', [
            'categorie_eleve' => $categorieEleve,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_eleve_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CategorieEleve $categorieEleve): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieEleve->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorieEleve);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_eleve_index');
    }
}
