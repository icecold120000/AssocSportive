<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\CategorieEleve;
use App\Entity\Fichier;
use App\Form\EleveType;
use App\Form\FichierType;
use App\Form\FilterEleveType;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Ang3\Component\Serializer\Encoder\ExcelEncoder;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/eleve")
 */
class EleveController extends AbstractController
{
    /**
     * @Route("/", name="eleve_index", methods={"GET","POST"})
     */
    public function index(EleveRepository $eleveRepo, Request $request,
     PaginatorInterface $paginator): Response
    {

        $eleves = $eleveRepo->findAll();

        $form = $this->createForm(FilterEleveType::class);
        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $eleves = $eleveRepo->search(
                $search->get('classe')->getData(),
                $search->get('genreEleve')->getData(),
                $search->get('archiveEleve')->getData()
            );
        }

        $eleves = $paginator->paginate(
            $eleves,
            $request->query->getInt('page',1),
            50
        );
        
        return $this->render('eleve/index.html.twig', [
            'eleves' => $eleves,
            'form' => $form->createView(),
        ]);
    }

    private string $eleveFileDirectory;

    public function __construct(EntityManagerInterface $entityManager, EleveRepository $eleveRepository,
     string $eleveFileDirectory)
    {
        $this->entityManager = $entityManager;
        $this->eleveRepository = $eleveRepository;
        $this->eleveFileDirectory = $eleveFileDirectory;
    }

    /**
     * @Route("/file", name="eleve_file", methods={"GET","POST"})
     */
    public function fileSubmit(Request $request, SluggerInterface $slugger): Response
    {
        $eleveFile = new Fichier();
        $form = $this->createForm(FichierType::class, $eleveFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $fichierEleve */
            $fichierEleve = $form->get('fileSubmit')->getData();

            if ($fichierEleve) {
                $originalFilename = pathinfo($fichierEleve->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$fichierEleve->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $fichierEleve->move(
                        $this->getParameter('eleveFile_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre liste");
                }

                $eleveFile->setFichierNom($newFilename);
            }
            // updates the 'brochureFilename' property to store the CSV or XLSX file name
            // instead of its contents

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eleveFile);
            $entityManager->flush();

            EleveController::creerEleves($eleveFile->getFichierNom());

            $this->addFlash(
                'SuccessFileSubmit',
                'Vos élèves ont été sauvegardés'
            );

            return $this->redirectToRoute('eleve_file');
        }

        return $this->render('eleve/eleveFile.html.twig',[
            'fichierUser' => $eleveFile,
            'form' => $form->createView(),
        ]);
    }

    public function getDataFromFile(string $fileName): array
    {
        $file = $this->eleveFileDirectory . $fileName;

        $fileExtension =pathinfo($file, PATHINFO_EXTENSION);

        $normalizers = [new ObjectNormalizer()];

        $encoders=[
            new ExcelEncoder($defaultContext = []),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        /** @var string $fileString */
        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $fileExtension);

        return $data;

    }

    private function creerEleves(string $fileName): void
    {

        $eleveCreated = 0;

        /* Parcours le tableau donnée par le fichier Excel*/
        while($eleveCreated < sizeof($this->getDataFromFile($fileName))){
            /*Pour chaque Eleve*/
            foreach($this->getDataFromFile($fileName) as $row) {
                /*Parcours les données d'un élève*/
                foreach ($row as $rowData) {
                    /*Verifie s'il existe un colonne email et qu'elle n'est pas vide*/
                    if(array_key_exists('Identifiant Elève', $rowData)
                     && !empty($rowData['Identifiant Elève']))
                    {
                        /*Recherche l'élève dans la base de donnée*/

                        $eleve = $this->eleveRepository->
                        findOneEleveBy($rowData['Nom'],
                            $rowData['Prénom'],
                            new \DateTime($rowData['Date de naissance']
                        ));

                        if($eleve === null)
                        {
                            $eleve = new Eleve();

                            $today = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

                            $birthday = new \DateTime($rowData['Date de naissance']);

                            $sexe = $rowData['Sexe'];

                            $interval = $today->diff($birthday);
                            
                            $total = $interval->format('%y%');
                            if(23 >= $total && 18 <= $total)
                            {
                                switch ($sexe) {
                                    case 'M':
                                        $categorie = $this->getDoctrine()->getRepository
                                        (CategorieEleve::class)->findOneByLibelleCat("Junior Garçon");
                                        break;
                                    
                                    case 'F':
                                        $categorie = $this->getDoctrine()->getRepository
                                        (CategorieEleve::class)->findOneByLibelleCat("Junior Fille");
                                        break;
                                }
                            }
                            elseif(18 > $total && 14 <= $total)
                            {
                                switch ($sexe) {
                                    case 'M':
                                        $categorie = $this->getDoctrine()->getRepository
                                        (CategorieEleve::class)->findOneByLibelleCat("Cadet");
                                        break;               
                                    case 'F':
                                        $categorie = $this->getDoctrine()->getRepository
                                        (CategorieEleve::class)->findOneByLibelleCat("Cadette");
                                        break;
                                }
                            }
                            else{
                                $categorie = null;
                            }

                            $eleve->setDateCreation($today);

                            $classe = $this->getDoctrine()->getRepository
                            (Classe::class)->findOneByLibelle($rowData['Libellé classe']);

                            $eleve
                                ->setNomEleve($rowData['Nom'])
                                ->setPrenomEleve($rowData['Prénom'])
                                ->setGenreEleve($sexe)
                                ->setDateNaissance($birthday)
                                ->setArchiveEleve(false)
                                ->setClasse($classe)
                                ->setCategorie($categorie)
                                ->setNumTelEleve($rowData['Numéro de Téléphone'])
                                ->setNumTelParent($rowData['Numéro de Téléphone d\'un Parent']);

                            $this->entityManager->persist($eleve);

                            $eleveCreated++;
                        }
                        /*S'il n'existe pas alors on le créer
                         en tant qu'un nouvel élève*/
                    }
                }
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @Route("/new", name="eleve_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $eleve = new Eleve();
        $eleve->setDateCreation(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $form = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imgProfileEleve */
            $imgProfileEleve = $form->get('imgFile')->getData();

            if ($imgProfileEleve) {
                $originalFilename = pathinfo($imgProfileEleve->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFileNameImg = $safeFilename.'.'.$imgProfileEleve->guessExtension();

                // Move the file to the directory where photos are stored
                try {
                    $imgProfileEleve->move(
                        $this->getParameter('imgProfil_directory'),
                        $newFileNameImg
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre fichier");
                }

                $eleve->setPhotoEleve($newFileNameImg);
            }
            
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eleve);

            $this->addFlash(
                'SuccessEleve',
                'Votre élève a été sauvegardé'
            );

            return $this->redirectToRoute('eleve_new');
        }

        return $this->render('eleve/new.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eleve_show", methods={"GET"})
     */
    public function show(Eleve $eleve): Response
    {
        return $this->render('eleve/show.html.twig', [
            'eleve' => $eleve,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="eleve_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Eleve $eleve, SluggerInterface $slugger): Response
    {
        $eleve->setDateMaj(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $form = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imgProfileEleve */
            $imgProfileEleve = $form->get('imgFile')->getData();

            if ($imgProfileEleve) {
                $originalFilename = pathinfo($imgProfileEleve->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFileNameImg = $safeFilename.'.'.$imgProfileEleve->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imgProfileEleve->move(
                        $this->getParameter('imgProfil_directory'),
                        $newFileNameImg
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre fichier");
                }

                $eleve->setPhotoEleve($newFileNameImg);
            }
            
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
                'SuccessEleve',
                'Votre élève a été modifié'
            );

            return $this->redirectToRoute('eleve_edit', array('id' => $eleve->getId()));
        }

        return $this->render('eleve/edit.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="eleve_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Eleve $eleve): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eleve->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eleve);
            $entityManager->flush();
        }

        return $this->redirectToRoute('eleve_index');
    }
}
