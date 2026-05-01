<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Form\IngredientType;
use App\Service\FileUploader;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RecetteController extends AbstractController
{
    #[Route('/recettes', name: 'recette_index', methods: ['GET'])]
    public function index(RecetteRepository $repo): Response
    {
        $recettes = $repo->findBy([], ['dateCreation' => 'DESC']);

        return $this->render('recette/index.html.twig', ['recettes' => $recettes]);
    }

    #[Route('/recettes/{id}/favoris/ajouter', name: 'recette_favorite_add', methods: ['POST'])]
    public function addFavorite(Recette $recette, Request $request, SessionInterface $session): Response
    {
        $favorites = $session->get('favorites', []);
        if (!in_array($recette->getId(), $favorites, true)) {
            $favorites[] = $recette->getId();
            $session->set('favorites', $favorites);
        }

        return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
    }

    #[Route('/recettes/{id}/favoris/retirer', name: 'recette_favorite_remove', methods: ['POST'])]
    public function removeFavorite(Recette $recette, Request $request, SessionInterface $session): Response
    {
        $favorites = $session->get('favorites', []);
        $favorites = array_values(array_filter($favorites, fn($id) => $id !== $recette->getId()));
        $session->set('favorites', $favorites);

        return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
    }

    #[Route('/mes-favoris', name: 'mes_favoris', methods: ['GET'])]
    public function myFavorites(RecetteRepository $repo, SessionInterface $session): Response
    {
        $favorites = $session->get('favorites', []);
        $recettes = $favorites ? $repo->findBy(['id' => $favorites]) : [];

        return $this->render('recette/favorites.html.twig', ['recettes' => $recettes]);
    }

    #[Route('/recettes/{id}', name: 'recette_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        return $this->render('recette/show.html.twig', ['recette' => $recette]);
    }

    #[Route('/recettes/nouvelle', name: 'recette_new', methods: ['GET','POST'])]
    #[IsGranted('ROLE_CUISINIER')]
    public function new(Request $request, EntityManagerInterface $em, FileUploader $uploader): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // handle file
            $image = $form->get('image')->getData();
            if ($image) {
                $fileName = $uploader->upload($image);
                $recette->setImageName($fileName);
            }

            // auteur
            $recette->setAuteur($this->getUser());

            $em->persist($recette);
            $em->flush();

            $this->addFlash('success', 'Recette créée.');
            return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
        }

        return $this->render('recette/new.html.twig', ['recetteForm' => $form->createView()]);
    }

    #[Route('/recettes/{id}/modifier', name: 'recette_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Recette $recette, EntityManagerInterface $em, FileUploader $uploader): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $recette);

        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                // remove old
                if ($recette->getImageName()) {
                    $uploader->remove($recette->getImageName());
                }
                $fileName = $uploader->upload($image);
                $recette->setImageName($fileName);
            }

            $em->flush();
            $this->addFlash('success', 'Recette modifiée.');
            return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
        }

        return $this->render('recette/edit.html.twig', ['recetteForm' => $form->createView(), 'recette' => $recette]);
    }

    #[Route('/recettes/{id}/supprimer', name: 'recette_delete', methods: ['POST'])]
    public function delete(Request $request, Recette $recette, EntityManagerInterface $em, FileUploader $uploader): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $recette);

        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
            if ($recette->getImageName()) {
                $uploader->remove($recette->getImageName());
            }
            $em->remove($recette);
            $em->flush();
            $this->addFlash('success', 'Recette supprimée.');
        }

        return $this->redirectToRoute('recette_index');
    }
}
