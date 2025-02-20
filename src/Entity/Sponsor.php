<?php

namespace App\Entity;

use App\Repository\SponsorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SponsorRepository::class)]
class Sponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Veuillez entrer votre nom")]
    private ?string $sponsor_name = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Veuillez entrer le montant")]
    #[Assert\Type(
        type: "integer",
        message: "Le montant doit être un nombre entier."    )]
    private ?int $montant = null;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'sponsors')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSponsorName(): ?string
    {
        return $this->sponsor_name;
    }

    public function setSponsorName(string $sponsor_name): static
    {
        $this->sponsor_name = $sponsor_name;
        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;
        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }   

    public function setEvent(?Event $event): static
    {
        $this->event = $event;
        return $this;
    }
}
