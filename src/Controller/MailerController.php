<?php
// src/Controller/MailerController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
/**
 * Contrôleur pour tout ce qui est la gestion des email
 */
class MailerController extends AbstractController
{
    /**
     * Méthode de test pour l'envoi d'email
     * 
     * @route /mailer
     * @name app_mailer
     * 
     * @param MailerInterface $mailer (dépendance) Service d'envoi d'emails
     * 
     * @return Response Message de confirmation d'envoi
     */
    #[Route('/mailer', name: 'app_mailer')]
    public function sendEmail(MailerInterface $mailer)
    {
        $email = (new Email())
        // simple string
        ->from('mailtrap@example.com')
        ->to('test@hotmail.fr')
        ->subject('Objet du mail')
        ->text('Envoyer des e-mails est à nouveau amusant !') // Format TEXT
        ->html('<p>Voir l\'intégration Twig pour une meilleure intégration HTML !</p>');
        // >htmlTemplate('emails/test.html.twig')->context([
        //     'name' => 'Alex'
        //     ]);
        $mailer->send($email);
        return new Response(
          'Email bien envoyer'
       );
    }
}