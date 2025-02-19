<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'eventsT')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userE = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $updateAt = null;

    #[ORM\OneToMany(targetEntity: EventContact::class, mappedBy: 'event', cascade: ['persist', 'remove'])]
    private Collection $contact;

    #[ORM\OneToMany(targetEntity: Reminder::class, mappedBy: 'eventR', cascade: ['persist', 'remove'])]
    private Collection $reminders;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isImportant = false;

    public function __construct()
    {
        $this->contact = new ArrayCollection();
        $this->reminders = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable(); // Automatycznie ustawiana data utworzenia
        $this->updateAt = new \DateTime(); // Automatycznie ustawiana data aktualizacji
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserE(): ?User
    {
        return $this->userE;
    }

    public function setUserE(?User $userE): static
    {
        $this->userE = $userE;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface|string $date): static
    {
        if (is_string($date)) {
            $dateObject = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
            if (!$dateObject) {
                throw new \InvalidArgumentException("Invalid date format, expected 'Y-m-d H:i:s'.");
            }
            $this->date = $dateObject;
        } else {
            $this->date = $date;
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt): static
    {
        $this->updateAt = $updateAt;
        return $this;
    }

    public function getContact(): Collection
    {
        return $this->contact;
    }

    public function addContact(EventContact $contact): static
    {
        if (!$this->contact->contains($contact)) {
            $this->contact->add($contact);
            $contact->setEvent($this);
        }

        return $this;
    }

    public function removeContact(EventContact $contact): static
    {
        if ($this->contact->removeElement($contact)) {
            if ($contact->getEvent() === $this) {
                $contact->setEvent(null);
            }
        }

        return $this;
    }

    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    public function addReminder(Reminder $reminder): static
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders->add($reminder);
            $reminder->setEventR($this);
        }

        return $this;
    }

    public function removeReminder(Reminder $reminder): static
    {
        if ($this->reminders->removeElement($reminder)) {
            if ($reminder->getEventR() === $this) {
                $reminder->setEventR(null);
            }
        }

        return $this;
    }

    public function getIsImportant(): bool
    {
        return $this->isImportant;
    }

    public function setIsImportant(bool $isImportant): self
    {
        $this->isImportant = $isImportant;
        return $this;
    }
}
