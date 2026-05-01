<?php

namespace App\Service;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;

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
        $all = $this->repo->findBy(['publiee' => true]);
        return count($all);
    }

    public function getRecettesParCategorie(): array
    {
        $all = $this->repo->findAll();
        $res = [];
        foreach ($all as $r) {
            $cat = $r->getCategorie() ? $r->getCategorie()->getNom() : 'Sans catégorie';
            $res[$cat] = ($res[$cat] ?? 0) + 1;
        }

        return $res;
    }

    public function getMoyenneIngredients(): float
    {
        $all = $this->repo->findAll();
        if (count($all) === 0) {
            return 0.0;
        }

        $totalIngredients = 0;
        foreach ($all as $r) {
            $totalIngredients += count($r->getIngredients());
        }

        return $totalIngredients / count($all);
    }
}
