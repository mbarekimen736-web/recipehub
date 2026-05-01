<?php

namespace App\Repository;

use App\Entity\Recette;
use App\Entity\CategorieRecette;
use App\Entity\TagRecette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recette::class);
    }

    public function findByFilters($titre, $categorie, $difficulte, $tag): array
    {
        $qb = $this->createQueryBuilder('r');

        if ($titre) {
            $qb->andWhere('r.titre LIKE :titre')
               ->setParameter('titre', "%$titre%");
        }

        if ($categorie) {
            $qb->andWhere('r.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }

        if ($difficulte) {
            $qb->andWhere('r.difficulte = :difficulte')
               ->setParameter('difficulte', $difficulte);
        }

        if ($tag) {
            $qb->join('r.tags', 't')
               ->andWhere('t.id = :tag')
               ->setParameter('tag', $tag);
        }

        return $qb->orderBy('r.dateCreation', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
}