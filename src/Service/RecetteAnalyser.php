<?php

namespace App\Service;

use App\Entity\Recette;
use App\Repository\RecetteRepository;

class RecetteAnalyser
{
    public function __construct(private RecetteRepository $repo)
    {
    }

    public function getTempsTotal(Recette $r): int
    {
        return $r->getTempsPreparation() + ($r->getTempsCuisson() ?? 0);
    }

    public function getTotalRecettesPubliees(): int
    {
        return count($this->repo->findBy(['publiee' => true]));
    }

    public function getRecettesParCategorie(): array
    {
        $all = $this->repo->findAll();
        $res = [];

        foreach ($all as $r) {
            $res['Sans catégorie'] = ($res['Sans catégorie'] ?? 0) + 1;
        }

        return $res;
    }

    public function getMoyenneIngredients(): float
    {
        $all = $this->repo->findAll();

        if (count($all) === 0) {
            return 0;
        }

        $total = 0;

        foreach ($all as $r) {
            $total += method_exists($r, 'getIngredients') ? count($r->getIngredients()) : 0;
        }

        return $total / count($all);
    }
}