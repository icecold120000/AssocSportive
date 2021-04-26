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
                $search->get('type')->getData(),
                $search->get('categorieEleve')->getData(),
                $search->get('sport')->getData(),
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
            // prend la valeur de l'image soumis dans le formulaire
            $imgEventFile = $form->get('imgEvent')->getData();

            // vérifie si la valeur sous dans la formulaire n'est pas nul
            if ($imgEventFile) {

                // sauvegarde le nom du fichier
                $originalFilename = pathinfo($imgEventFile->getClientOriginalName(), PATHINFO_FILENAME);

                // permet avec sans risque d'inclure le nom du fichier en une partie de l'url 
                $safeFilename = $slugger->slug($originalFilename);

                // permet de recuperer le nom du fichier et son extension et de créer l'url du fichier 
                $newFilenameImg = $safeFilename.'.'.$imgEventFile->guessExtension();

                // permet de deplacer le fichier dans le dossier où il sera stockée
                try {
                    $imgEventFile->move(
                        $this->getParameter('eventImg_directory'),
                        $newFilenameImg
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre image");
                }
                // permet de stocker l'url du fichier dans la base de données
                $evenement->setImageEvenement($newFilenameImg);
            }

            $vigEventFile = $form->get('vigEvent')->getData();

            if ($vigEventFile) {
                $originalFilename = pathinfo($vigEventFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilenameVig = $safeFilename.'.'.$vigEventFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $vigEventFile->move(
                        $this->getParameter('eventVig_directory'),
                        $newFilenameVig
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre vignette");
                }
                $evenement->setVignetteEvenement($newFilenameVig);
            }            

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

        //Créer un nouvelle instance d'une inscription
        $inscription = new Inscription();

        // Rechercher dans la base de donnée s'il existe une inscription avec le même l'élève dans le même événement 
        $foundEleve = $this->getDoctrine()->getRepository(Inscription::class)
        ->findOneInscritption($eleve->getId(),$evenement->getId());

        // s'il n'existe aucune inscription de cet élève dans cet événement
        if($foundEleve === null)
        {
            // objet prend les instances de l'élève et de l'événement 
            $inscription
                        ->setEvenement($evenement)
                        ->setEleve($eleve)
                        ->setDateInscription(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $entityManager = $this->getDoctrine()->getManager();

            // sauvegarde l'instance de l'inscription
            $entityManager->persist($inscription);
            // le créer dans la base de données
            $entityManager->flush();
        }

        // accéder à la page de confirmation d'inscription
        return $this->render('evenement/confirm_inscrip.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @Route("/{id}/desinscription/{eleveId}", name="evenement_desinscription", methods={"DELETE"})
     * @Entity("eleve", expr="repository.find(eleveId)")
     */
    public function desinscription(Request $request, Evenement $evenement, Eleve $eleve): Response
    {
        $inscription = $this->getDoctrine()->getRepository(Inscription::class)->findOneInscritption($eleve->getId(),$evenement->getId());

        if ($this->isCsrfTokenValid('delete'.$inscription->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($inscription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evenement_show', array('id' => $evenement->getId()) );
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

                $evenement->setImageEvenement($newFilename);
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

                $evenement->setVignetteEvenement($newFilename);
            }
            // updates the 'brochureFilename' property to store the PNG/JPEG/JPG file name
            // instead of its contents
            

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