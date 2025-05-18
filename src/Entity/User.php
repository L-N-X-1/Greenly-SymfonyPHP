<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullName = null;
  
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imgLink = null;

    #[Vich\UploadableField(mapping: 'imgLink', fileNameProperty: 'imgLink')]
    private $imgFile;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $verifedEntreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $verifedAssoc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $verifedForm = null;

    #[ORM\Column(name: "created_at", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $createdAt;

    #[ORM\Column(name: "timestamp", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $timestamp;

    #[ORM\Column(name: "updated_at", type: "datetime", nullable: true)]
    protected $updatedAt;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $verifedDonne = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'user')]
    private Collection $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }
    
    public function setRoles(array|string $roles): self
    {
        $this->roles = (array) $roles; // Ensure it's always an array
        return $this;
    }
    

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getImgLink(): ?string
    {
        return $this->imgLink;
    }

    public function setImgLink(?string $imgLink): static
    {
        $this->imgLink = $imgLink;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImgFile(): ?File
    {
        return $this->imgFile;
    }

    /**
     * @param File|null $imgFile
     * @return $this
     */
    public function setImgFile(?File $imgFile): self
    {
        $this->imgFile = $imgFile;

        if (null !== $imgFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getVerifedEntreprise(): ?string
    {
        return $this->verifedEntreprise;
    }

    public function setVerifedEntreprise(?string $verifedEntreprise): static
    {
        $this->verifedEntreprise = $verifedEntreprise;

        return $this;
    }

    public function getVerifedAssoc(): ?string
    {
        return $this->verifedAssoc;
    }

    public function setVerifedAssoc(?string $verifedAssoc): static
    {
        $this->verifedAssoc = $verifedAssoc;

        return $this;
    }

    public function getVerifedForm(): ?string
    {
        return $this->verifedForm;
    }

    public function setVerifedForm(?string $verifedForm): static
    {
        $this->verifedForm = $verifedForm;

        return $this;
    }
    
    /**
     * Get creation date
     *
     * @return \DateTime|null
     */
    final public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime|null
     */
    final public function getTimestamp() : \DateTime
    {
        return $this->timestamp;
    }

    /**
     * Get update date
     *
     * @return \DateTime|null
     */
    final public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    final public function doSoftTimestableOnPrePersist()
    {
        $this->createdAt = new \DateTime('now');
        $this->timestamp = new \DateTime('now');
    }

    #[ORM\PreUpdate]
    final public function doSoftTimestableOnPreUpdate()
    {
        $this->updatedAt = new \DateTime('now');
        $this->timestamp = new \DateTime('now');
    }

    public function getVerifedDonne(): ?string
    {
        return $this->verifedDonne;
    }

    public function setVerifedDonne(?string $verifedDonne): static
    {
        $this->verifedDonne = $verifedDonne;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

}
