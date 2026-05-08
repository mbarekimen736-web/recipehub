<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use App\Service\FileUploader;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/recette')]
class RecetteController extends AbstractController
{
    #[Route('/', name: 'recette_index')]
    public function index(RecetteRepository $recetteRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $recettes = $recetteRepository->findAll();
        } else {
            $recettes = $recetteRepository->findBy(['publiee' => true]);
        }
        
        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    #[Route('/new', name: 'recette_new')]
    #[IsGranted('ROLE_CUISINIER')]
    public function new(Request $request, EntityManagerInterface $em, FileUploader $fileUploader, NotificationService $notificationService): Response
    {
        $recette = new Recette();
        $recette->setAuteur($this->getUser());
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recette->setDateCreation(new \DateTime());
            
            // Gestion de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageName = $fileUploader->upload($imageFile);
                $recette->setImageName($imageName);
            }
            
            $isPublished = $recette->isPubliee();

            $em->persist($recette);
            $em->flush();

            if ($isPublished) {
                try {
                    $notificationService->notifierNouvelleRecette($recette);
                    $this->addFlash('success', '✅ Recette créée et publiée !');
                } catch (\Exception $e) {
                    $this->addFlash('warning', '⚠️ Recette créée mais l\'email a échoué.');
                }
            } else {
                $this->addFlash('success', '📝 Recette créée en brouillon.');
            }

            return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
        }

        return $this->render('recette/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'recette_show')]
    public function show(Recette $recette): Response
    {
        if (!$recette->isPubliee() && 
            !$this->isGranted('ROLE_ADMIN') && 
            $this->getUser() !== $recette->getAuteur()) {
            throw $this->createNotFoundException('Recette non disponible.');
        }

        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }

    #[Route('/{id}/edit', name: 'recette_edit')]
    #[IsGranted('ROLE_CUISINIER')]
    public function edit(Recette $recette, Request $request, EntityManagerInterface $em, FileUploader $fileUploader, NotificationService $notificationService): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $recette->getAuteur()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette recette.');
        }

        $wasPublished = $recette->isPubliee();
        $oldImage = $recette->getImageName();
        
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de la nouvelle image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Supprimer l'ancienne image
                if ($oldImage) {
                    $fileUploader->remove($oldImage);
                }
                // Uploader la nouvelle
                $newImageName = $fileUploader->upload($imageFile);
                $recette->setImageName($newImageName);
            }

            $em->flush();

            if (!$wasPublished && $recette->isPubliee()) {
                try {
                    $notificationService->notifierNouvelleRecette($recette);
                    $this->addFlash('success', '✅ Recette publiée !');
                } catch (\Exception $e) {
                    $this->addFlash('warning', '⚠️ Recette modifiée mais l\'email a échoué.');
                }
            } else {
                $this->addFlash('success', '✏️ Recette modifiée avec succès.');
            }

            return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
        }

        return $this->render('recette/edit.html.twig', [
            'form' => $form->createView(),
            'recette' => $recette,
        ]);
    }

    #[Route('/{id}/delete', name: 'recette_delete', methods: ['POST'])]
    #[IsGranted('ROLE_CUISINIER')]
    public function delete(Recette $recette, Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $recette->getAuteur()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette recette.');
        }

        if ($this->isCsrfTokenValid('delete' . $recette->getId(), $request->request->get('_token'))) {
            // Supprimer l'image associée
            if ($recette->getImageName()) {
                $fileUploader->remove($recette->getImageName());
            }
            
            $em->remove($recette);
            $em->flush();
            $this->addFlash('success', '🗑️ Recette supprimée avec succès.');
        } else {
            $this->addFlash('error', '❌ Token CSRF invalide.');
        }

        return $this->redirectToRoute('recette_index');
    }
    #[Route('/setup-categories', name: 'setup_categories')]
public function setupCategories(EntityManagerInterface $em): Response
{
    $categories = [
        ['nom' => 'Plat', 'icone' => '🍝', 'description' => 'Plats principaux'],
        ['nom' => 'Entrée', 'icone' => '🥗', 'description' => 'Entrées et apéritifs'],
        ['nom' => 'Dessert', 'icone' => '🍰', 'description' => 'Desserts sucrés'],
        ['nom' => 'Boisson', 'icone' => '🥤', 'description' => 'Boissons et smoothies'],
        ['nom' => 'Snack', 'icone' => '🍕', 'description' => 'Snacks et en-cas'],
        ['nom' => 'Soupe', 'icone' => '🥣', 'description' => 'Soupes et potages'],
    ];
    
    foreach ($categories as $catData) {
        $categorie = new \App\Entity\CategorieRecette();
        $categorie->setNom($catData['nom']);
        $categorie->setIcone($catData['icone']);
        $categorie->setDescription($catData['description']);
        $em->persist($categorie);
    }
    
    $em->flush();
    
    return new Response('✅ 6 catégories créées avec succès ! <a href="/recette/new">Retour à la création</a>');
}
}