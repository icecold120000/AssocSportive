<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Form\SearchDocType;
use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/document")
 */
class DocumentController extends AbstractController
{
    /**
     * @Route("/", name="document_index", methods={"GET","POST"})
     */
    public function index(DocumentRepository $documentRepo,Request $request, PaginatorInterface $paginator): Response
    {
        $documents = $documentRepo->findAll();
        
        $form = $this->createForm(SearchDocType::class);

        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documents = $documentRepo->search(
                $search->get('mots')->getData()
            );
        }

        $documents = $paginator->paginate(
            $documents,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('document/index.html.twig',[
            'form' => $form->createView(),
            'documents' => $documents,
        ]);
    }

    /**
     * @Route("/admin", name="document_admin", methods={"GET","POST"})
     */
    public function admin(DocumentRepository $documentRepo, Request $request, PaginatorInterface $paginator): Response
    {
        $documents = $documentRepo->findAll();
        
        $form = $this->createForm(SearchDocType::class);

        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documents = $documentRepo->search(
                $search->get('mots')->getData()
            );
        }

        $documents = $paginator->paginate(
            $documents,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('document/admin.html.twig',[
            'form' => $form->createView(),
            'documents' => $documents,
        ]);
    }

    /**
     * @Route("/new", name="document_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        
        $document = new Document();
        $document->setDateAjout(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $fichierDocument */
            $fichierDocument = $form->get('file')->getData();

            if ($fichierDocument) {
                $originalFilename = pathinfo($fichierDocument->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$fichierDocument->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $fichierDocument->move(
                        $this->getParameter('docs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre fichier");
                }
                $document->setLienDocument($newFilename);
            }            

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($document);
            $entityManager->flush();

            $this->addFlash(
                'SuccessDoc',
                'Votre document a été sauvegardé'
            );

            return $this->redirectToRoute('document_new');
        }

        return $this->render('document/new.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="document_show", methods={"GET"})
     */
    public function show(Document $document): Response
    {
        return $this->render('document/show.html.twig', [
            'document' => $document,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="document_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Document $document, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $fichierDocument = $form->get('file')->getData();

            if ($fichierDocument) {
                $originalFilename = pathinfo($fichierDocument->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$fichierDocument->guessExtension();
 

                // Move the file to the directory where brochures are stored
                try {
                    $fichierDocument->move(
                        $this->getParameter('docs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre fichier");
                }
                $document->setLienDocument($newFilename);
            }
            // updates the 'LienDocument' property to store the PDF file name
            // instead of its contents
            
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash(
                'SuccessDoc',
                'Votre document a été modifié'
            );

            return $this->redirectToRoute('document_edit', array('id' => $document->getId()));
        }

        return $this->render('document/edit.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/delete", name="document_delete_view", methods={"GET"})
     */
    public function deleteView(Document $document): Response
    {
        return $this->render('document/delete_view.html.twig', [
            'document' => $document,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="document_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Document $document): Response
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(),
         $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($document);
            $entityManager->flush();
        }

        return $this->redirectToRoute('document_admin');
    }

}
