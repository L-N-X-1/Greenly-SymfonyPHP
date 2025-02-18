<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom du module est obligatoire")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom du module ne doit pas dépasser 255 caractères"
    )]
    private ?string $nom_module = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description du module est obligatoire")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La description ne doit pas dépasser 255 caractères"
    )]
    private ?string $description_module = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre d'heures est obligatoire")]
    #[Assert\Positive(message: "Le nombre d'heures doit être un nombre positif")]
    private ?int $nb_heures = null;
    

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le niveau est obligatoire")]
    private ?string $niveau = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "La catégorie est obligatoire")]
    private ?string $categorie = null;

    #[ORM\Column]
    private ?bool $statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de création est obligatoire")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "La date de création doit être valide")]
        private ?\DateTimeInterface $datecreation_module = null;

    /**
     * @var Collection<int, Formation>
     */
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'module', cascade: ['remove'], orphanRemoval: true)]
    private Collection $formations;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomModule(): ?string
    {
        return $this->nom_module;
    }

    public function setNomModule(?string $nom_module): static
    {
        $this->nom_module = $nom_module;
        return $this;
    }
    

    public function getDescriptionModule(): ?string
    {
        return $this->description_module;
    }

    public function setDescriptionModule(?string $description_module): static
    {
        $this->description_module = $description_module;
        return $this;
    }
    

    public function getNbHeures(): ?int
    {
        return $this->nb_heures;
    }

    public function setNbHeures(?int $nb_heures): self
    {
        $this->nb_heures = $nb_heures;  // Accepte null ou un entier
        return $this;
    }
    

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(?string $niveau): static
    {
        $this->niveau = $niveau;
    
        return $this;
    }
    

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): static
{
    $this->categorie = $categorie;
    return $this;
}


    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDatecreationModule(): ?\DateTimeInterface
    {
        return $this->datecreation_module;
    }

    public function setDatecreationModule(?\DateTimeInterface $datecreation_module): static
{
    $this->datecreation_module = $datecreation_module;
    
    return $this;
}

    
    

    /**
     * @return Collection<int, Formation>
     */
    
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setModule($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getModule() === $this) {
                $formation->setModule(null);
            }
        }

        return $this;
    }
}
