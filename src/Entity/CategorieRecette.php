<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['categorie:read']]
        ),
        new Get(
            normalizationContext: ['groups' => ['categorie:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['categorie:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['categorie:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['categorie:read']],
    denormalizationContext: ['groups' => ['categorie:write']]
)]
class CategorieRecette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['categorie:read', 'recette:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[Groups(['recette:read', 'categorie:read', 'categorie:write'])]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Groups(['categorie:read', 'categorie:write'])]
    private ?string $icone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['categorie:read', 'categorie:write'])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Recette::class)]
    private Collection $recettes;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIcone(): ?string
    {
        return $this->icone;
    }

    public function setIcone(?string $icone): self
    {
        $this->icone = $icone;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /** @return Collection|Recette[] */
    #[Groups(['categorie:read'])]
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): self
    {
        if (! $this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->setCategorie($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): self
    {
        if ($this->recettes->removeElement($recette)) {
            if ($recette->getCategorie() === $this) {
                $recette->setCategorie(null);
            }
        }

        return $this;
    }
}