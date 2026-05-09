<?php

namespace App\Controller;

use App\Service\RecetteAnalyser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    #[Route('/{_locale}/', name: 'homepage_locale', requirements: ['_locale' => 'fr|en|ar|tn'])]
    public function index(RecetteAnalyser $analyser): Response
    {
        $stats = [
            'totalPubliees' => $analyser->getTotalRecettesPubliees(),
            'parCategorie' => $analyser->getRecettesParCategorie(),
            'moyenneIngredients' => $analyser->getMoyenneIngredients(),
            'tempsMoyenPreparation' => $analyser->getTempsMoyenPreparation(),
            'parDifficulte' => $analyser->getRecettesParDifficulte(),
        ];

        return $this->render('homepage/index.html.twig', ['stats' => $stats]);
    }
}