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
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class OubliMdpController extends AbstractController
{
    /**
     * @Route("/oubli/mdp", name="oubli_mdp", methods={"GET","POST"})
     */
    public function index(Request $request, MailerInterface $mailer, UserRepository $userRepo): Response
    {
        $user = new User();
        $form = $this->createForm(OubliMdpType::class, $user);
        $form->handleRequest($request);

        $userEmail = $form->get('email')->getData();

        $user = $userRepo->findOneByEmail($userEmail);   

        if($form->isSubmitted() && $form->isValid()) {

            OubliMdpController::sendEmail($mailer,$userEmail);

            return $this->redirectToRoute('oubli_send');
        }

        return $this->render('oubli_mdp/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function sendEmail(MailerInterface $mailer, User $user)
    {
        
        $email = (new Email())
            ->from($user->getEmail())
            ->to('assocstvincent@gmail.com')
            ->subject('Demande de réinitialisation de mot de passe')
            ->text('réinitialisation de mot de passe')
            ->html('<p>J\'ai oublié mon mot de passe 
                pouvez vous le réinitialiser </p>');
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new Exception("Erreur votre mail n\'a pas été envoyer.
                Veuillez saisir une adresse valide", $e); 
        }
    }

    /**
     * @Route("/oubli/mdp/send", name="oubli_send")
     */
    public function send(): Response
    {
        return $this->render('oubli_mdp/send.html.twig');
    }

}
