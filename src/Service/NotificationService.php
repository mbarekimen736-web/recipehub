<?php

namespace App\Service;

use App\Entity\Recette;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class NotificationService
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function notifierNouvelleRecette(Recette $recette): void
    {
        // Envoyer à l'admin (vous pouvez changer l'adresse)
        $adminEmail = 'admin@recipehub.com';
        
        $email = (new TemplatedEmail())
            ->from('noreply@recipehub.com')
            ->to($adminEmail)
            ->subject('🍽️ Nouvelle recette : ' . $recette->getTitre())
            ->htmlTemplate('emails/nouvelle_recette.html.twig')
            ->context([
                'recette' => $recette,
            ]);
        
        $this->mailer->send($email);
    }
}