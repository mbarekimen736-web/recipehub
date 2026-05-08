<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['recette:read']]
        ),
        new Get(
            normalizationContext: ['groups' => ['recette:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['recette:write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['recette:write']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['recette:read']],
    denormalizationContext: ['groups' => ['recette:write']]
)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recette:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recette:read', 'recette:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    private ?string $titre = null;

    #[ORM\Column(type: "text")]
    #[Groups(['recette:read', 'recette:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 30)]
    private ?string $description = null;

    #[ORM\Column(type: "text")]
    #[Groups(['recette:read', 'recette:write'])]
    #[Assert\NotBlank]
    private ?string $instructions = null;

    #[ORM\Column]
    #[Groups(['recette:read', 'recette:write'])]
    #[Assert\Range(min: 1)]
    private ?int $tempsPreparation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['recette:read', 'recette:write'])]
    private ?int $tempsCuisson = null;

    #[ORM\Column(length: 20)]
    #[Groups(['recette:read', 'recette:write'])]
    private ?string $difficulte = null;

    #[ORM\Column]
    #[Groups(['recette:read', 'recette:write'])]
    #[Assert\Range(min: 1, max: 50)]
    private ?int $nbPersonnes = null;

    #[ORM\Column]
    #[Groups(['recette:read'])]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column]
    #[Groups(['recette:read'])]
    private ?bool $publiee = null;

    #[ORM\ManyToOne(targetEntity: CategorieRecette::class, inversedBy: 'recettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieRecette $categorie = null;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Ingredient::class, cascade: ['persist', 'remove'])]
    private Collection $ingredients;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $auteur = null;
// src/Entity/Recette.php

#[ORM\Column(length: 255, nullable: true)]
private ?string $imageName = null;

// Getters et setters
public function getImageName(): ?string
{
    return $this->imageName;
}

public function setImageName(?string $imageName): self
{
    $this->imageName = $imageName;
    return $this;
}

// Méthode utilitaire pour le chemin complet de l'image
public function getImagePath(): ?string
{
    if (!$this->imageName) {
        return null;
    }
    return '/uploads/recettes/' . $this->imageName;
}
    #[ORM\ManyToMany(targetEntity: TagRecette::class, inversedBy: 'recettes')]
    private Collection $tags;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->publiee = false;
    }

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getInstructions(): ?string { return $this->instructions; }
    public function setInstructions(string $instructions): self { $this->instructions = $instructions; return $this; }

    public function getTempsPreparation(): ?int { return $this->tempsPreparation; }
    public function setTempsPreparation(int $t): self { $this->tempsPreparation = $t; return $this; }

    public function getTempsCuisson(): ?int { return $this->tempsCuisson; }
    public function setTempsCuisson(?int $t): self { $this->tempsCuisson = $t; return $this; }

    public function getDifficulte(): ?string { return $this->difficulte; }
    public function setDifficulte(string $d): self { $this->difficulte = $d; return $this; }

    public function getNbPersonnes(): ?int { return $this->nbPersonnes; }
    public function setNbPersonnes(int $n): self { $this->nbPersonnes = $n; return $this; }

    public function getDateCreation(): ?\DateTime { return $this->dateCreation; }
    public function setDateCreation(\DateTime $d): self { $this->dateCreation = $d; return $this; }

    public function isPubliee(): ?bool { return $this->publiee; }
    public function setPubliee(bool $p): self { $this->publiee = $p; return $this; }

    public function getCategorie(): ?CategorieRecette { return $this->categorie; }
    public function setCategorie(?CategorieRecette $categorie): self { $this->categorie = $categorie; return $this; }

    public function getIngredients(): Collection { return $this->ingredients; }
    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setRecette($this);
        }
        return $this;
    }
    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            if ($ingredient->getRecette() === $this) {
                $ingredient->setRecette(null);
            }
        }
        return $this;
    }

    public function getAuteur(): ?User { return $this->auteur; }
    public function setAuteur(?User $auteur): self { $this->auteur = $auteur; return $this; }

    public function getTags(): Collection { return $this->tags; }
    public function addTag(TagRecette $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
        return $this;
    }
    public function removeTag(TagRecette $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    /**
     * Méthodes pour l'API - exposent les données des relations
     */
    #[Groups(['recette:read'])]
    public function getCategorieNom(): ?string
    {
        return $this->categorie?->getNom();
    }

    #[Groups(['recette:read'])]
    public function getIngredientsList(): array
    {
        return $this->ingredients->map(function($ingredient) {
            return $ingredient->getNom() . ': ' . $ingredient->getQuantite();
        })->toArray();
    }

    #[Groups(['recette:read'])]
    public function getTagsNames(): array
    {
        return $this->tags->map(function($tag) {
            return $tag->getNom();
        })->toArray();
    }

    #[Groups(['recette:read'])]
    public function getAuteurPseudo(): ?string
    {
        return $this->auteur?->getPseudo();
    }
}