<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $event_id = null;

    #[ORM\Column(length: 255)]
    private ?string $event_name = null;

    #[ORM\Column(length: 1000)]
    private ?string $event_description = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $event_date = null;

    #[ORM\Column(length: 255)]
    private ?string $event_location = null;

    #[ORM\Column(length: 50)]
    private ?string $event_statut = null;

    #[ORM\Column]
    private ?int $organizer_id = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Sponsor::class)]
    private Collection $sponsors;

    public function __construct()
    {
        $this->sponsors = new ArrayCollection();
    }

    public function getEventId(): ?int
    {
        return $this->event_id;
    }

    public function getEventName(): ?string
    {
        return $this->event_name;
    }

    public function setEventName(string $event_name): static
    {
        $this->event_name = $event_name;
        return $this;
    }

    public function getEventDescription(): ?string
    {
        return $this->event_description;
    }

    public function setEventDescription(string $event_description): static
    {
        $this->event_description = $event_description;
        return $this;
    }

    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->event_date;
    }

    public function setEventDate(\DateTimeInterface $event_date): static
    {
        $this->event_date = $event_date;
        return $this;
    }

    public function getEventLocation(): ?string
    {
        return $this->event_location;
    }

    public function setEventLocation(string $event_location): static
    {
        $this->event_location = $event_location;
        return $this;
    }

    public function getEventStatut(): ?string
    {
        return $this->event_statut;
    }

    public function setEventStatut(string $event_statut): static
    {
        $this->event_statut = $event_statut;
        return $this;
    }

    public function getOrganizerId(): ?int
    {
        return $this->organizer_id;
    }

    public function setOrganizerId(int $organizer_id): static
    {
        $this->organizer_id = $organizer_id;
        return $this;
    }

    /**
     * @return Collection<int, Sponsor>
     */
    public function getSponsors(): Collection
    {
        return $this->sponsors;
    }

    public function addSponsor(Sponsor $sponsor): static
    {
        if (!$this->sponsors->contains($sponsor)) {
            $this->sponsors->add($sponsor);
            $sponsor->setEvent($this);
        }
        return $this;
    }

    public function removeSponsor(Sponsor $sponsor): static
    {
        if ($this->sponsors->removeElement($sponsor)) {
            if ($sponsor->getEvent() === $this) {
                $sponsor->setEvent(null);
            }
        }
        return $this;
    }
}
