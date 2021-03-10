<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerController extends AbstractController
{
    /**
     * @Route("/email")
     */
    public function sendEmail(MailerInterface $mailer)
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
     * @Route("/email", name="email_index")
     */
    public function index(): Response
    {
        return $this->render('oubli_mdp/oubli.html.twig', [
        ]);
    }
}
