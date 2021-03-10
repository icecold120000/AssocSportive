<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Eleve;
use App\Entity\Fichier;
use App\Form\UserType;
use App\Form\FichierType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Ang3\Component\Serializer\Encoder\ExcelEncoder;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $encoder;

    public function __construct(EntityManagerInterface $entityManager,
     UserRepository $userRepository,
     UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->userFileDirectory = $userFileDirectory;
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(Request $request,UserRepository $userRepository,
     PaginatorInterface $paginator): Response
    {

        $users = $userRepository->findAll();

        $users = $paginator->paginate(
            $users,
            $request->query->getInt('page',1),
            50
        );

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'SuccessUser',
                'Votre utilisateur a été sauvegardé'
            );

            return $this->redirectToRoute('user_new');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete"), name="user_delete_view", methods={"GET"})
     */
    public function delete_view(User $user): Response
    {
        return $this->render('user/delete_view.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/file", name="user_file", methods={"GET","POST"})
     */
    public function fileSubmit(Request $request, SluggerInterface $slugger): Response
    {
        $userFile = new Fichier();
        $form = $this->createForm(FichierType::class, $userFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var UploadedFile $fichierUser */
            $fichierUser = $form->get('fileSubmit')->getData();

            if ($fichierUser) {
                $originalFilename = pathinfo($fichierUser->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$fichierUser->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $fichierUser->move(
                        $this->getParameter('userFile_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException("Fichier corrompu. Veuillez retransferer votre liste");
                }
            }
            // updates the 'brochureFilename' property to store the CSV or XLSX file name
            // instead of its contents
            $userFile->setFichierNom($newFilename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userFile);
            $entityManager->flush();

            UserController::creerUsers($userFile->getFichierNom());

            $this->addFlash(
                'SuccessFileSubmit',
                'Vos utilisateurs ont été sauvegardés'
            );

            return $this->redirectToRoute('user_file');
        }

        return $this->render('user/userFile.html.twig',[
            'fichierUser' => $userFile,
            'form' => $form->createView(),
        ]);
    }

    public function getDataFromFile(string $fileName): array
    {
        $file = $this->userFileDirectory . $fileName;

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

    private function creerUsers(string $fileName): void
    {
        $userCreated = 0;
        /* Parcours le tableau donnée par le fichier Excel*/
        while($userCreated < sizeof($this->getDataFromFile($fileName))) {
            /*Pour chaque Utilisateur*/
            foreach($this->getDataFromFile($fileName) as $row) {
                /*Parcours les données d'un utilisateur*/
                foreach ($row as $rowData) {
                    /*Verifie s'il existe un colonne email et qu'elle n'est pas vide*/
                    if(array_key_exists('email de l\'utilisateur',$rowData)
                     && !empty($rowData['email de l\'utilisateur']))
                    {
                        /*Recherche l'utilisateur dans la base de donnée*/
                        $user = $this->userRepository->findOneBy([
                            'email' => $rowData['email de l\'utilisateur']
                        ]);
                        /*S'il n'existe pas alors on le créer
                         en tant qu'un nouvel utilisateur*/
                        if($user === null)
                        {
                            $user = new User();

                            $idUser = $rowData['identifiant de l\'utilisateur'];
                            $password = $rowData['mot de passe de l\'utilisateur'];
                            $roleUser = $rowData['rôle de l\'utilisateur'];

                            $eleve = $this->getDoctrine()->getRepository(Eleve::class)
                            ->findOneByFirstAndLastName($rowData['nom de l\'utilisateur']
                                ,$rowData['prénom de l\'utilisateur']);

                            $user
                                ->setId($idUser)
                                ->setEmail($rowData['email de l\'utilisateur'])
                                ->setNomUser($rowData['nom de l\'utilisateur'])
                                ->setPrenomUser($rowData['prénom de l\'utilisateur'])
                                ->setEleve($eleve);

                            switch ($roleUser) {
                                case "admin":
                                    $user->setRoles([User::ROLE_ADMIN]);
                                    break;
                                case "eleve":
                                    $user->setRoles([User::ROLE_ELEVE]);
                                    break;
                                case "enseignant":
                                    $user->setRoles([User::ROLE_ENSEIGNANT]);
                                    break;
                                case "comptable":
                                    $user->setRoles([User::ROLE_COMPTABLE]);
                                    break;
                                case "utilisateur":
                                    $user->setRoles([User::ROLE_USER]);
                                    break;                                
                                default:
                                    $user->setRoles([User::ROLE_USER]);
                                    break;
                            }

                            $user->setPassword($this->encoder->encodePassword($user, $password));
                                
                            $this->entityManager->persist($user);

                            $metadata = $this->entityManager->getClassMetaData(get_class($user));
                            $metadata->setIdGeneratorType(\Doctrine\ORM
                                \Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
                            $userCreated++;
                        }
                    }
                }
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'SuccessUser',
                'Votre utilisateur a été modifié'
            );

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
