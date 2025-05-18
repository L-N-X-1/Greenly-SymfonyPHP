<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Nom de l'evenement is required")]
    #[Assert\Length(min:3 ,max:20,minMessage: "Le nom doit avoir au moins {{ limit }} caractères", maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères")] 
    private ?string $event_name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Veuillez entrer une description")]
    #[Assert\Length(min:20, minMessage:"description tres courte")]
    private ?string $event_description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date de l'événement est requise")]
    #[Assert\Type(\DateTimeInterface::class, message: "La valeur doit être une date valide")]
    #[Assert\GreaterThan("today", message: "La date de l'événement doit être dans le futur")]
    private ?\DateTimeInterface $event_date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez choisir une gouvernorat")]
    private ?string $event_location = null;


    public const STATUS_EN_COURS = 'En cours';
    public const STATUS_PLANIFIE = 'Planifié';
    public const STATUS_ACHEVE = 'Achevée';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut de l'événement est requis.")]
    private ?string $event_status = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Sponsor::class, cascade: ['persist', 'remove'])]
    private Collection $sponsors;

    /**
     * @var Collection<int, Attendee>
     */
    #[ORM\OneToMany(targetEntity: Attendee::class, mappedBy: 'event')]
    private Collection $attendees;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $googleEventId = null;
    
    #[ORM\Column(nullable: true)]
    private ?int $likes = 0;

    #[ORM\Column(nullable: true)]
    private ?int $dislikes = 0;

    private const MAX_ATTENDEES = 50;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?User $user = null;

    public function __construct()
    {
        $this->sponsors = new ArrayCollection();
        $this->attendees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEventStatus(): ?string
    {
        return $this->event_status;
    }

    public function setEventStatus(string $event_status): self
    {
        $this->event_status = $event_status;
        return $this;
    }

    /**
     * @return Collection<int, Sponsor>
     */
    public function getSponsors(): Collection
    {
        return $this->sponsors;
    }

    /**
     * @return Collection<int, Attendee>
     */
    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    public function addAttendee(Attendee $attendee): static
    {
        if ($this->attendees->count() >= self::MAX_ATTENDEES) {
            throw new \RuntimeException('Event has reached maximum capacity of ' . self::MAX_ATTENDEES . ' attendees.');
        }

        if (!$this->attendees->contains($attendee)) {
            $this->attendees->add($attendee);
            $attendee->setEvent($this);
        }

        return $this;
    }

    public function removeAttendee(Attendee $attendee): static
    {
        if ($this->attendees->removeElement($attendee)) {
            // set the owning side to null (unless already changed)
            if ($attendee->getEvent() === $this) {
                $attendee->setEvent(null);
            }
        }

        return $this;
    }
    public function getGoogleEventId(): ?string
    {
        return $this->googleEventId;
    }

    public function setGoogleEventId(?string $googleEventId): static
    {
        $this->googleEventId = $googleEventId;
        return $this;
    }

    public function isAtCapacity(): bool
    {
        return $this->attendees->count() >= self::MAX_ATTENDEES;
    }

    public function getRemainingCapacity(): int
    {
        return self::MAX_ATTENDEES - $this->attendees->count();
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;
        return $this;
    }

    public function incrementLikes(): self
    {
        $this->likes++;
        return $this;
    }

    public function getDislikes(): int
    {
        return $this->dislikes;
    }

    public function setDislikes(int $dislikes): self
    {
        $this->dislikes = $dislikes;
        return $this;
    }

    public function incrementDislikes(): self
    {
        $this->dislikes++;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
