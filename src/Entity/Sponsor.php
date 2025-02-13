<?php

namespace App\Entity;

use App\Repository\SponsorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SponsorRepository::class)]
class Sponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $sponsor_id = null;

    #[ORM\Column(length: 255)]
    private ?string $sponsor_name = null;

    #[ORM\ManyToOne(inversedBy: 'sponsors')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'event_id')]
    private ?Event $event = null;

    #[ORM\Column]
    private ?int $product_id = null;

    #[ORM\Column]
    private ?int $montant = null;

    public function getSponsorId(): ?int
    {
        return $this->sponsor_id;
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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;
        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    public function setProductId(int $product_id): static
    {
        $this->product_id = $product_id;
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
}
