<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $name = null; // <--- NOWE POLE

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var Collection<int, RecurringEvent>
     */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: RecurringEvent::class, cascade: ['persist', 'remove'])]
    private Collection $recurringEvents;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Setting::class)]
    private ?Setting $setting = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $resetToken = null;

    public function __construct()
    {
        $this->recurringEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): ?string // <--- GETTER
    {
        return $this->name;
    }

    public function setName(?string $name): self // <--- SETTER
    {
        $this->name = $name;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): self
    {
        $this->password = $hashedPassword;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function eraseCredentials(): void
    {
        // Wyczyść poufne dane
    }

    /**
     * @return Collection<int, RecurringEvent>
     */
    public function getRecurringEvents(): Collection
    {
        return $this->recurringEvents;
    }

    public function addRecurringEvent(RecurringEvent $recurringEvent): self
    {
        if (!$this->recurringEvents->contains($recurringEvent)) {
            $this->recurringEvents->add($recurringEvent);
            $recurringEvent->setOwner($this);
        }

        return $this;
    }

    public function removeRecurringEvent(RecurringEvent $recurringEvent): self
    {
        if ($this->recurringEvents->removeElement($recurringEvent)) {
            if ($recurringEvent->getOwner() === $this) {
                $recurringEvent->setOwner(null);
            }
        }

        return $this;
    }
    public function getSetting(): ?Setting
    {
        return $this->setting;
    }

    public function setSetting(?Setting $setting): self
    {
        $this->setting = $setting;

        // Upewniamy się, że po stronie Setting także jest ustawiony user:
        if ($setting && $setting->getUser() !== $this) {
            $setting->setUser($this);
        }

        return $this;
    }


    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }
}

