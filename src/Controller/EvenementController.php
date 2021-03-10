<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Form\EvenementType;
use App\Form\FilterEventType;
use App\Repository\EvenementRepository;
use App\Repository\DocumentRepository;
use App\Repository\InscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * @Route("/evenement")
 */
class EvenementController extends AbstractController
{
    /**
     * @Route("/", name="evenement_index", methods={"GET","POST"})
     */
    public function index(EvenementRepository $eventRepo,
     Request $request, PaginatorInterface $paginator): Response
    {

        $evenements = $eventRepo->findAll();

        $form = $this->createForm(FilterEventType::class);
        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $evenements = $eventRepo->search(
                $search->get('Type')->getData(),
                $search->get('CategorieEleve')->getData(),
                $search->get('Sport')->getData(),
                $search->get('actif')->getData()
            );
        }

        $evenements = $paginator->paginate(
            $evenements,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin", name="evenement_admin", methods={"GET"})
     */
    public function admin(EvenementRepository $eventRepo,
     Request $request, PaginatorInterface $paginator): Response
    {

        $evenements = $eventRepo->findAll();
        $evenements = $paginator->paginate(
            $evenements,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('evenement/admin.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/new", name="evenement_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imgEventFile */
            $imgEventFile = $form->get('imgEvent')->getData();

            if ($imgEventFile) {
                $originalFilename = pathinfo($imgEventFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$imgEventFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imgEventFile->move(
                        $this->getParameter('eventImg_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre image");
                }
            }

            $vigEventFile = $form->get('vigEvent')->getData();

            if ($vigEventFile) {
                $originalFilename = pathinfo($vigEventFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$vigEventFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $vigEventFile->move(
                        $this->getParameter('eventVig_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre vignette");
                }
            }
            // updates the 'brochureFilename' property to store the PNG/JPEG/JPG file name
            // instead of its contents
            $evenement->setImageEvenement($newFilename);
            $evenement->setVignetteEvenement($newFilename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash(
                'SuccessEvent',
                'Votre événement a été sauvegardé'
            );

            return $this->redirectToRoute('evenement_new');
        }
        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evenement_show", methods={"GET"})
     */
    public function show(Evenement $evenement,DocumentRepository $documents, InscriptionRepository $inscription): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
            'documents' => $documents->findAll(),
            'inscriptions' => $inscription->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/confirmInscription/{eleveId}", name="evenement_inscrire")
     * @Entity("eleve", expr="repository.find(eleveId)")
     */
    public function inscrire(Evenement $evenement, Eleve $eleve): Response
    {

        $inscription = new Inscription();

        $foundEleve = $this->getDoctrine()->getRepository(Inscription::class)->find($eleve->getId());

        if($foundEleve === null)
        {
            $inscription
                        ->setEvenement($evenement)
                        ->setEleve($eleve)
                        ->setDateInscription(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($inscription);
            $entityManager->flush();
        }

        return $this->render('evenement/confirm_inscrip.html.twig', [
            'evenement' => $evenement,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="evenement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Evenement $evenement ,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imgEventFile */
            $imgEventFile = $form->get('imgEvent')->getData();

            if ($imgEventFile) {
                $originalFilename = pathinfo($imgEventFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$imgEventFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imgEventFile->move(
                        $this->getParameter('eventImg_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre image");
                }
            }

            $vigEventFile = $form->get('vigEvent')->getData();

            if ($vigEventFile) {
                $originalFilename = pathinfo($vigEventFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$vigEventFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $vigEventFile->move(
                        $this->getParameter('eventVig_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre vignette");
                }
            }
            // updates the 'brochureFilename' property to store the PNG/JPEG/JPG file name
            // instead of its contents
            $evenement->setImageEvenement($newFilename);
            $evenement->setVignetteEvenement($newFilename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
                'SuccessEvent',
                'Votre événement a été modifié'
            );

            return $this->redirectToRoute('evenement_edit', array('id' => $evenement->getId()));
        }
        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="evenement_delete_view", methods={"GET"})
     */
    public function delete_view(Evenement $evenement): Response
    {
        return $this->render('evenement/delete_view.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="evenement_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Evenement $evenement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evenement);
            $entityManager->flush();
        }
        return $this->redirectToRoute('evenement_admin');
    }
}