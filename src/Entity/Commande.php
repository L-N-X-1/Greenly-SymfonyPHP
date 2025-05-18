<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\HasLifecycleCallbacks] // Assurez-vous que la classe exécute les événements du cycle de vie
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'id de l'utilisateur est obligatoire.")]
    private ?string $id_u = null;

    #[ORM\Column]
    private ?float $montant = null; // Changer en float pour correspondre au prix du produit

    #[ORM\Column(length: 255)]
    private ?string $statut = 'non traité'; // Valeur par défaut

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^\d{8}$/",
        message: "Le numéro de téléphone doit contenir exactement 8 chiffres."
    )]
    #[Assert\Regex(
        pattern: "/^[1-9]+$/",
        message: "Le numéro de téléphone ne doit contenir que des chiffres."
    )]
private ?string $numerot = null;


    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    public function __construct()
    {
        $this->statut = 'non traité'; // Définit le statut par défaut dès la création
    }

    #[ORM\PrePersist]
    public function setDefaults(): void
    {
        if ($this->date === null) {
            $this->date = new \DateTime();
        }

        if ($this->statut === null) {
            $this->statut = 'non traité';
        }

        if ($this->produit !== null) {
            $this->montant = $this->produit->getPrix(); // Assigne le prix du produit au montant
        }
    }

    // GETTERS & SETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdU(): ?string
    {
        return $this->id_u;
    }

    public function setIdU(string $id_u): static
    {
        $this->id_u = $id_u;
        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getNumerot(): ?int
    {
        return $this->numerot;
    }

    public function setNumerot(int $numerot): static
    {
        $this->numerot = $numerot;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        if ($produit !== null) {
            $this->montant = $produit->getPrix(); // Assigne automatiquement le prix du produit
        }
        return $this;
    }
}
