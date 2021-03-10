<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\OubliMdpType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

class OubliMdpController extends AbstractController
{
    /**
     * @Route("/oubli/mdp", name="oubli_mdp", methods={"GET","POST"})
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(OubliMdpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            MailerController::sendEmail($mailer,$user);

            return $this->redirectToRoute('oubli_send');
        }

        return $this->render('oubli_mdp/index.html.twig', [
            'utilisateur' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/oubli/mdp", name="oubli_send")
     */
    public function send(): Response
    {
        return $this->render('oubli_mdp/oubli_send.html.twig');
    }

}
