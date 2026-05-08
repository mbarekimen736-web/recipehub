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

    // src/Repository/RecetteRepository.php

public function findByFilters(?string $titre, ?CategorieRecette $categorie, ?string $difficulte, ?TagRecette $tag): \Doctrine\ORM\QueryBuilder
{
    $qb = $this->createQueryBuilder('r')
        ->leftJoin('r.tags', 't');

    if ($titre) {
        $qb->andWhere('r.titre LIKE :titre')
           ->setParameter('titre', '%' . $titre . '%');
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
        $qb->andWhere('t.id = :tag')
           ->setParameter('tag', $tag);
    }

    return $qb->orderBy('r.dateCreation', 'DESC');
}
       
}