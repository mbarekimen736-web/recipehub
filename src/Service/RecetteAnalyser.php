<?php

namespace App\Service;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Repository\CategorieRecetteRepository;

class RecetteAnalyser
{
    public function __construct(
        private RecetteRepository $repo,
        private ?CategorieRecetteRepository $categorieRepo = null
    ) {
    }

    public function getTempsTotal(Recette $r): int
    {
        return $r->getTempsPreparation() + ($r->getTempsCuisson() ?? 0);
    }

    public function getTotalRecettesPubliees(): int
    {
        return $this->repo->count(['publiee' => true]);
    }

    public function getRecettesParCategorie(): array
    {
        $all = $this->repo->findAll();
        $res = [];

        foreach ($all as $r) {
            $categorie = $r->getCategorie();
            if ($categorie) {
                $nomCategorie = $categorie->getNom();
                $res[$nomCategorie] = ($res[$nomCategorie] ?? 0) + 1;
            } else {
                $res['Sans catégorie'] = ($res['Sans catégorie'] ?? 0) + 1;
            }
        }

        return $res;
    }

    public function getMoyenneIngredients(): float
    {
        $all = $this->repo->findAll();

        if (count($all) === 0) {
            return 0.0;
        }

        $total = 0;
        $count = 0;

        foreach ($all as $r) {
            $ingredients = $r->getIngredients();
            if ($ingredients && method_exists($ingredients, 'count')) {
                $total += $ingredients->count();
            }
            $count++;
        }

        return $count > 0 ? round($total / $count, 2) : 0.0;
    }

    public function getTempsMoyenPreparation(): float
    {
        $all = $this->repo->findAll();

        if (count($all) === 0) {
            return 0.0;
        }

        $total = 0;
        foreach ($all as $r) {
            $total += $r->getTempsPreparation();
        }

        return round($total / count($all), 2);
    }

    public function getRecettesParDifficulte(): array
    {
        $all = $this->repo->findAll();
        $res = ['facile' => 0, 'moyen' => 0, 'difficile' => 0];

        foreach ($all as $r) {
            $difficulte = $r->getDifficulte();
            if (isset($res[$difficulte])) {
                $res[$difficulte]++;
            }
        }

        return $res;
    }
}