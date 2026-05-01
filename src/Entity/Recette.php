<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: "text")]
    private ?string $description = null;

    #[ORM\Column(type: "text")]
    private ?string $instructions = null;

    #[ORM\Column]
    private ?int $tempsPreparation = null;

    #[ORM\Column(nullable: true)]
    private ?int $tempsCuisson = null;

    #[ORM\Column(length: 20)]
    private ?string $difficulte = null;

    #[ORM\Column]
    private ?int $nbPersonnes = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column]
    private ?bool $publiee = null;

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
}