<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la formation est requis.")]
    private ?string $nom_formation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est requise.")]
#[Assert\Length(min: 4, minMessage: "La description doit contenir au moins 4 caractères.")]
    private ?string $description_formation = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La durée est requise.")]
#[Assert\Positive(message: "La durée doit être un nombre positif.")]
    private ?int $duree_formation = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le mode de formation est requis.")]
    private ?string $mode_formation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de début est requise.")]
    #[Assert\Type("\DateTimeInterface")]

    private ?\DateTimeInterface $datedebut_formation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de début est requise.")]
    #[Assert\Type("\DateTimeInterface")]

    private ?\DateTimeInterface $datefin_formation = null;

    #[ORM\ManyToOne(inversedBy: 'formations')]
    #[Assert\NotNull(message: "Le module est requis.")]
    private ?Module $module = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFormation(): ?string
    {
        return $this->nom_formation;
    }

    public function setNomFormation(?string $nom_formation): static
    {
        $this->nom_formation = $nom_formation;

        return $this;
    }

    public function getDescriptionFormation(): ?string
    {
        return $this->description_formation;
    }

    public function setDescriptionFormation(?string $description_formation): static
    {
        $this->description_formation = $description_formation;

        return $this;
    }

    public function getDureeFormation(): ?int
    {
        return $this->duree_formation;
    }

    public function setDureeFormation(?int $duree_formation): self
    {
        $this->duree_formation = $duree_formation;
        return $this;
    }

    public function getModeFormation(): ?string
    {
        return $this->mode_formation;
    }

    public function setModeFormation(?string $mode_formation): self
{
    $this->mode_formation = $mode_formation;
    return $this;
}

    public function getDatedebutFormation(): ?\DateTimeInterface
    {
        return $this->datedebut_formation;
    }

    public function setDatedebutFormation(?\DateTimeInterface $datedebut_formation): self
    {
        $this->datedebut_formation = $datedebut_formation;
    
        return $this;
    }
    

    public function getDatefinFormation(): ?\DateTimeInterface
    {
        return $this->datefin_formation;
    }

    public function setDatefinFormation(?\DateTimeInterface $datefin_formation): self
    {
        $this->datefin_formation = $datefin_formation;
        return $this;
    }
    

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }
}
