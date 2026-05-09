<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('noreply@recipehub.com')
            ->to('mbarekimen736@gmail.com')
            ->subject('Test RecipeHub - Configuration réussie !')
            ->html('<h1>🎉 Félicitations !</h1>
                    <p>Votre configuration email fonctionne parfaitement.</p>
                    <p>Bienvenue sur RecipeHub !</p>
                    <hr>
                    <small>Email envoyé depuis Mailtrap</small>');

        $mailer->send($email);
        
        return $this->json(['message' => 'Email envoyé avec succès ! Vérifiez sur Mailtrap.io']);
    }
}
