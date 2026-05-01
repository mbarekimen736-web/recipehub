<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['recette:read']],
    denormalizationContext: ['groups' => ['recette:write']]
)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    #[Groups(['recette:read','recette:write'])]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 30)]
    #[Groups(['recette:read','recette:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['recette:read','recette:write'])]
    private ?string $instructions = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 1)]
    #[Groups(['recette:read','recette:write'])]
    private ?int $tempsPreparation = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['recette:read','recette:write'])]
    private ?int $tempsCuisson = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(choices: ['facile', 'moyen', 'difficile'])]
    #[Groups(['recette:read','recette:write'])]
    private ?string $difficulte = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 1, max: 50)]
    #[Groups(['recette:read','recette:write'])]
    private ?int $nbPersonnes = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['recette:read'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'boolean')]
    private bool $publiee = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['recette:read'])]
    private ?string $imageName = null;

    #[ORM\ManyToOne(targetEntity: CategorieRecette::class, inversedBy: 'recettes')]
    #[Groups(['recette:read','recette:write'])]
    private ?CategorieRecette $categorie = null;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Ingredient::class, cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $ingredients;

    #[ORM\ManyToMany(targetEntity: TagRecette::class, inversedBy: 'recettes', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'recette_tag')]
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'recettes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $auteur = null;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(string $instructions): self
    {
        $this->instructions = $instructions;

        return $this;
    }

    public function getTempsPreparation(): ?int
    {
        return $this->tempsPreparation;
    }

    public function setTempsPreparation(int $tempsPreparation): self
    {
        $this->tempsPreparation = $tempsPreparation;

        return $this;
    }

    public function getTempsCuisson(): ?int
    {
        return $this->tempsCuisson;
    }

    public function setTempsCuisson(?int $tempsCuisson): self
    {
        $this->tempsCuisson = $tempsCuisson;

        return $this;
    }

    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    public function setDifficulte(string $difficulte): self
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    public function getNbPersonnes(): ?int
    {
        return $this->nbPersonnes;
    }

    public function setNbPersonnes(int $nbPersonnes): self
    {
        $this->nbPersonnes = $nbPersonnes;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function isPubliee(): bool
    {
        return $this->publiee;
    }

    public function setPubliee(bool $publiee): self
    {
        $this->publiee = $publiee;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getCategorie(): ?CategorieRecette
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieRecette $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /** @return Collection|Ingredient[] */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (! $this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
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

    /** @return Collection|TagRecette[] */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(TagRecette $tag): self
    {
        if (! $this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(TagRecette $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }
}
