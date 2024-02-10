<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    uriTemplate: '/companies/{id}/services',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['service:read']],
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'services', fromClass: Company::class)
    ],
    order: ['createdAt' => 'DESC']
)]
#[ApiResource(
    uriTemplate: '/companies/{id}/users',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['user:read']],
            security: 'user.isAdmin() 
                or (object == user.getCompany() and user.isCompanyAdmin())'
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'users', fromClass: Company::class)
    ],
    order: ['createdAt' => 'DESC']
)]
#[ApiResource(
    uriTemplate: '/companies/{id}/invitations',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['invitation:read']],
            security: 'user.isAdmin() 
                or (object == user.getCompany() and user.isCompanyAdmin())'
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'invitations', fromClass: Company::class)
    ],
    order: ['createdAt' => 'DESC']
)]
#[UniqueEntity(fields: ['name'], message: 'Ce nom existe déjà')]
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['company:collection:read']],
        ),
        new Get(),
        new Post(
            security: 'user.isUser() and user.company == null'
        ),
        new Patch(
            denormalizationContext: ['groups' => ['company:update']],
            security: '(user.isCompanyAdmin() and object == user.getCompany()) or user.isAdmin()'
        )
    ],
    normalizationContext: ['groups' => ['company:read']],
    denormalizationContext: ['groups' => ['company:write']],
    order: ['createdAt' => 'DESC'],
)]
class Company implements TimestampableEntityInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['company:collection:read'])]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le nom doit avoir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Groups(['company:collection:read', 'company:read', 'company:write', 'company:update', 'service:read', 'reservation:read'])]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[Groups(['company:collection:read', 'company:read', 'company:write', 'company:update', 'company:update', 'service:read', 'reservation:read'])]
    private ?Media $mainMedia = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Media::class)]
    #[Groups(['company:read', 'company:write', 'reservation:read'])]
    private Collection $medias;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Invitation::class)]
    #[Groups('company:admin:read')]
    private Collection $invitations;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Service::class)]
    #[Groups(['company:read', 'company:admin:read'])]
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
    #[Groups(['company:read', 'admin:company:update'])]
    private ?CompanyStatusEnum $status = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    #[Groups('company:admin:read')]
    private Collection $users;

    #[ORM\Column(length: 255)]
    #[Groups(['company:read', 'company:admin:read'])]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:read', 'company:admin:read'])]
    private ?string $zipCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:read', 'company:admin:read'])]
    private ?string $city = null;

    #[ORM\Column]
    #[Groups(['company:read', 'company:admin:read'])]
    private ?float $lat = null;

    #[ORM\Column]
    #[Groups(['company:read', 'company:admin:read'])]
    private ?float $lng = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:read', 'company:admin:read'])]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'companies')]
    #[Groups(['company:collection:read', 'company:admin:read', 'company:read'])]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Availibility::class)]
    private Collection $availibilities;

    public function __construct()
    {
        $this->invitations = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->availibilities = new ArrayCollection();
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

    public function getMainMedia(): ?Media
    {
        return $this->mainMedia;
    }

    public function setMainMedia(?Media $mainMedia): static
    {
        $this->mainMedia = $mainMedia;

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

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): static
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            $media->setCompany($this);
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    #[Groups(['company:read', 'company:admin:read'])]
    public function getMinimumServicePrice(): float
    {
        return $this->getServices()->reduce(function (int $accumulator, Service $value) {
            $servicePrice = $value->getPrice();
            if (0 === $accumulator || $accumulator > $servicePrice) {
                return $servicePrice;
            }
            return $accumulator;
        }, 0);
    }

    #[Groups(['company:read', 'company:admin:read'])]
    public function getMaximumServicePrice(): float
    {
        return $this->getServices()->reduce(function (int $accumulator, Service $value) {
            $servicePrice = $value->getPrice();
            if ($accumulator < $servicePrice) {
                return $servicePrice;
            }
            return $accumulator;
        }, 0);
    }

    #[Groups(['company:collection:read', 'company:read', 'company:admin:read'])]
    public function getAverageServicesRatesFromCustomer(): ?float
    {
        $services = $this->getServices();

        $ratesTotalAndCount = $services->reduce(function (array $accumulator, Service $value) {
                $accumulator[ 'count' ] = $value->getRatesFromCustomersCountAndTotal()[ 'count' ];
                $accumulator[ 'total' ] = $value->getRatesFromCustomersCountAndTotal()[ 'total' ];

                return $accumulator;
            }, [
                'count' => 0,
                'total' => 0
            ]);
        if (0 === $ratesTotalAndCount['count']) {
            return null;
        }

        return $ratesTotalAndCount['total'] / $ratesTotalAndCount['count'];
    }

    /**
     * @return Collection<int, User>
     */
    #[Groups(['company:read'])]
    public function getCompanyActiveTroubleMakers(): array
    {
        return $this->users->filter(function (User $user) {
            return $user->isTroubleMaker() && $user->isActive();
        })->getValues();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Availibility>
     */
    public function getAvailibilities(): Collection
    {
        return $this->availibilities;
    }

    public function addAvailibility(Availibility $availibility): static
    {
        if (!$this->availibilities->contains($availibility)) {
            $this->availibilities->add($availibility);
            $availibility->setCompany($this);
        }

        return $this;
    }

    public function removeAvailibility(Availibility $availibility): static
    {
        if ($this->availibilities->removeElement($availibility)) {
            // set the owning side to null (unless already changed)
            if ($availibility->getCompany() === $this) {
                $availibility->setCompany(null);
            }
        }

        return $this;
    }
}
