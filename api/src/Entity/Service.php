<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\TimestampableTrait;
use App\Interface\TimestampableEntityInterface;
use App\Repository\ServiceRepository;
use App\State\CreateAndUpdateStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            securityPostDenormalize: "is_granted('SERVICE_CREATE', object)",
            processor: CreateAndUpdateStateProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => ['service:update']],
            securityPostDenormalize: "is_granted('SERVICE_EDIT', object)",
            processor: CreateAndUpdateStateProcessor::class,
        ),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['service:read']],
    denormalizationContext: ['groups' => ['service:write']],
    order: ['createdAt' => 'DESC'],
)]
class Service implements TimestampableEntityInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['company:read', 'rate:by-user:read', 'user:reservation:read', 'reservation:read'])]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['service:read', 'service:write', 'user:reservation:read', 'reservation:read'])]
    private ?Company $company = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Groups(['service:read', 'service:write', 'company:read', 'service:update'])]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le nom doit avoir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Groups(['service:read', 'service:write', 'reservation:read', 'company:read', 'rate:by-user:read', 'user:reservation:read', 'service:update'])]
    private ?string $name = null;

    #[ORM\Column()]
    #[Assert\Range(
        notInRangeMessage: "La durée d'un service doit être comprise entre 5 minutes et 24 heures.",
        min: 300,
        max: 86400
    )]
    #[Groups(['service:read', 'service:write', 'company:read', 'user:reservation:read', 'service:update'])]
    private ?int $duration = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 5,
        max: 800,
        minMessage: "La description doit avoir au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Groups(['service:read', 'service:write', 'company:read', 'service:update', 'reservation:read'])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Rate::class)]
    private Collection $rates;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->rates = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getCompany(): ?company
    {
        return $this->company;
    }

    public function setCompany(?company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

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

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setService($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getService() === $this) {
                $reservation->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rate>
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    public function addRate(Rate $rate): static
    {
        if (!$this->rates->contains($rate)) {
            $this->rates->add($rate);
            $rate->setService($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): static
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getService() === $this) {
                $rate->setService(null);
            }
        }

        return $this;
    }

    public function getRatesFromCustomersCountAndTotal(): array
    {
        $rates = $this->getRates();
        $ratesTotal = $rates->reduce(function (int $accumulator, Rate $value) {
            if ($value->isCustomerRate()) {
                return $accumulator + $value->getValue();
            }

            return $accumulator;
        }, 0);

        return [
            'count' => $rates->count(),
            'total' => $ratesTotal
        ];
    }
}
