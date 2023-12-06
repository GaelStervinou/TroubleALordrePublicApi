<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\TimestampableTrait;
use App\Enum\CompanyStatusEnum;
use App\Interface\TimestampableEntityInterface;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            security: 'user.isUser()'
        ),
        new Patch(
            security: '(user.isCompanyAdmin() and object == user.getCompany()) or user.isAdmin()'
        )
    ],
    normalizationContext: ['groups' => ['company:read']],
    denormalizationContext: ['groups' => ['company:write']],
    order: ['createdAt' => 'DESC'],
)]class Company implements TimestampableEntityInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le nom doit avoir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Groups(['company:read', 'company:write', 'service:read', 'reservation:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 5)]
    #[Assert\Length(
        min: 5,
        max: 5,
        minMessage: "Le kbis invalide",
        maxMessage: "Le kbis invalide"
    )]
    private ?string $kbis = null;

    #[ORM\ManyToOne]
    private ?Media $media = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Invitation::class)]
    private Collection $invitations;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Service::class)]
    private Collection $services;

    #[ORM\Column(length: 10, options: ['default' => CompanyStatusEnum::PENDING])]
    #[Assert\Choice(
        choices: [
            CompanyStatusEnum::PENDING,
            CompanyStatusEnum::ACTIVE,
            CompanyStatusEnum::BANNED,
            CompanyStatusEnum::DELETED
        ],
        message: "Le status n'est pas valide"
    )]
    private ?CompanyStatusEnum $status = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->invitations = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(string $kbis): static
    {
        $this->kbis = $kbis;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): static
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): static
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setCompany($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getCompany() === $this) {
                $invitation->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setCompany($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getCompany() === $this) {
                $service->setCompany(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?CompanyStatusEnum
    {
        return $this->status;
    }

    public function setStatus(CompanyStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === CompanyStatusEnum::ACTIVE;
    }

    public function isPending(): bool
    {
        return $this->status === CompanyStatusEnum::PENDING;
    }

    public function isBanned(): bool
    {
        return $this->status === CompanyStatusEnum::BANNED;
    }

    public function isDeleted(): bool
    {
        return $this->status === CompanyStatusEnum::DELETED;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }
}
