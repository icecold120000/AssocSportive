<?php

namespace App\Controller;

use App\Entity\CategorieDocument;
use App\Form\CategorieDocumentType;
use App\Repository\CategorieDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/categorie/document")
 */
class CategorieDocumentController extends AbstractController
{
    /**
     * @Route("/", name="categorie_document_index", methods={"GET"})
     */
    public function index(CategorieDocumentRepository $categDocRepo,
     PaginatorInterface $paginator,
     Request $request): Response
    {

        $categDoc = $categDocRepo->findAll();

        $categDoc = $paginator->paginate(
            $categDoc,
            $request->query->getInt('page',1),
            10
        );


        return $this->render('categorie_document/index.html.twig', [
            'categorie_documents' => $categDoc,
        ]);
    }

    /**
     * @Route("/new", name="categorie_document_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $categorieDocument = new CategorieDocument();
        $form = $this->createForm(CategorieDocumentType::class, $categorieDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorieDocument);
            $entityManager->flush();

            $this->addFlash(
                'SuccessCategDoc',
                'Votre catégorie de document a été sauvegardé'
            );

            return $this->redirectToRoute('categorie_document_new');
        }

        return $this->render('categorie_document/new.html.twig', [
            'categorie_document' => $categorieDocument,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_document_show", methods={"GET"})
     */
    public function show(CategorieDocument $categorieDocument): Response
    {
        return $this->render('categorie_document/show.html.twig', [
            'categorie_document' => $categorieDocument,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categorie_document_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CategorieDocument $categorieDocument): Response
    {
        $form = $this->createForm(CategorieDocumentType::class, $categorieDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'SuccessCategDoc',
                'Votre catégorie de document a été modifié'
            );

            return $this->redirectToRoute('categorie_document_edit', array('id' => $categorieDocument->getId()));
        }

        return $this->render('categorie_document/edit.html.twig', [
            'categorie_document' => $categorieDocument,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_document_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CategorieDocument $categorieDocument): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieDocument->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorieDocument);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_document_index');
    }
}
