<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/favoris')]
class FavorisController extends AbstractController
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    #[Route('/ajouter/{id}', name: 'favoris_ajouter', methods: ['POST', 'GET'])]
    public function ajouter(Recette $recette): Response
    {
        $session = $this->requestStack->getSession();
        $favoris = $session->get('favoris', []);
        
        // Ajouter l'ID si pas déjà présent
        if (!in_array($recette->getId(), $favoris)) {
            $favoris[] = $recette->getId();
            $session->set('favoris', $favoris);
            $this->addFlash('success', '⭐ Recette ajoutée aux favoris !');
        } else {
            $this->addFlash('info', '📌 Cette recette est déjà dans vos favoris.');
        }
        
        return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
    }

    #[Route('/retirer/{id}', name: 'favoris_retirer', methods: ['POST', 'GET'])]
    public function retirer(Recette $recette): Response
    {
        $session = $this->requestStack->getSession();
        $favoris = $session->get('favoris', []);
        
        $key = array_search($recette->getId(), $favoris);
        if ($key !== false) {
            unset($favoris[$key]);
            $session->set('favoris', array_values($favoris));
            $this->addFlash('success', '🗑️ Recette retirée des favoris.');
        }
        
        return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
    }

    #[Route('/liste', name: 'favoris_liste')]
    public function liste(RecetteRepository $recetteRepository): Response
    {
        $session = $this->requestStack->getSession();
        $favorisIds = $session->get('favoris', []);
        
        $recettes = [];
        if (!empty($favorisIds)) {
            $recettes = $recetteRepository->findBy(['id' => $favorisIds]);
        }
        
        return $this->render('favoris/index.html.twig', [
            'recettes' => $recettes,
            'favorisIds' => $favorisIds,
        ]);
    }

    #[Route('/vider', name: 'favoris_vider', methods: ['POST'])]
    public function vider(): Response
    {
        $session = $this->requestStack->getSession();
        $session->set('favoris', []);
        $this->addFlash('success', '🧹 Tous les favoris ont été supprimés.');
        
        return $this->redirectToRoute('favoris_liste');
    }
}