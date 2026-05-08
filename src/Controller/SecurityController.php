<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/create', name: 'create_user')]
public function createUser(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
{
    $user = new User();
    $user->setEmail('test@test.com');
    $user->setPseudo('test');
    $user->setRoles(['ROLE_CUISINIER']);
    $user->setPassword($passwordHasher->hashPassword($user, 'test123'));
    
    $em->persist($user);
    $em->flush();
    
    return new Response('Utilisateur créé: test@test.com / test123');
}
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/setup-user', name: 'setup_user')]
    public function setupUser(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Supprimer l'utilisateur s'il existe
        $existing = $em->getRepository(User::class)->findOneBy(['email' => 'sarramikka@gmail.com']);
        if ($existing) {
            $em->remove($existing);
            $em->flush();
        }
        
        // Créer le nouvel utilisateur
        $user = new User();
        $user->setEmail('sarramikka@gmail.com');
        $user->setPseudo('sarramikka');
        $user->setRoles(['ROLE_CUISINIER']);
        $hashedPassword = $passwordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);
        
        $em->persist($user);
        $em->flush();
        
        return new Response('✅ Utilisateur créé avec succès ! <br> Email: sarramikka@gmail.com <br> Mot de passe: 123456 <br><br> <a href="/login">Aller à la page de connexion</a>');
    }
}