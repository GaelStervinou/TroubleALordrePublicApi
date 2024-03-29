<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\TimestampableTrait;
use App\Enum\ReservationStatusEnum;
use App\Interface\TimestampableEntityInterface;
use App\Repository\ReservationRepository;
use App\State\Reservation\CreateReservationSateProcessor;
use App\State\UpdateReservationStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    uriTemplate: '/users/{id}/reservations',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['user:reservation:read']],
            securityMessage: "Vous n'avez pas accès à cette ressource",
            name: Reservation::USER_RESERVATIONS_AS_CUSTOMERS,
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'reservations', fromClass: User::class)
    ],
    order: ['createdAt' => 'DESC']
)]
#[ApiResource(
    uriTemplate: '/users/trouble-maker/{id}/reservations',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['user:reservation:read']],
            name: Reservation::USER_RESERVATIONS_AS_TROUBLE_MAKERS,
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'reservationsTroubleMaker', fromClass: User::class)
    ],
    order: ['createdAt' => 'DESC']
)]
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'user.isAdmin()'
        ),
        new Get(
            security: '(user.isTroubleMaker() and object.getTroubleMaker() == user)
                or (user.isCompanyAdmin() and object.getTroubleMaker().getCompany() == user.getCompany())
                or user == object.getCustomer()
                or user.isAdmin()'
        ),
        new Post(
            security: 'user.isUser()',
            processor: CreateReservationSateProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => ['reservation:update']],
            security: '(user.isTroubleMaker() and object.getTroubleMaker() == user)
                or (user.isCompanyAdmin() and object.getTroubleMaker().getCompany() == user.getCompany())
                or user == object.getCustomer()',
            processor: UpdateReservationStateProcessor::class,
        )
    ],
    normalizationContext: ['groups' => ['reservation:read']],
    denormalizationContext: ['groups' => ['reservation:write']],
    order: ['createdAt' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',
])]
class Reservation implements TimestampableEntityInterface
{
    use TimestampableTrait;

    public const USER_RESERVATIONS_AS_CUSTOMERS = 'users_reservations_as_customer';
    public const USER_RESERVATIONS_AS_TROUBLE_MAKERS = 'users_reservations_as_trouble_maker';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['user:reservation:read', 'reservation:read'])]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentIntentId = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "La description doit avoir au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?string $description = null;

    #[ORM\Column()]
    #[Assert\GreaterThan('today', message: "La date ne peut pas être antérieure à aujourd'hui")]
    #[Groups(['reservation:write', 'reservation:read', 'user:reservation:read'])]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 50, options: ['default' => ReservationStatusEnum::ACTIVE])]
    #[Assert\Choice(
        choices: [
            ReservationStatusEnum::ACTIVE,
            ReservationStatusEnum::CANCELED,
            ReservationStatusEnum::FINISHED,
            ReservationStatusEnum::REFUNDED,
        ],
        message: "Le status n'est pas valide"
    )]
    #[Groups(['reservation:update', 'user:reservation:read', 'reservation:read', 'reservation:write'])]
    private ?ReservationStatusEnum $status = null;

    #[ORM\Column()]
    #[Groups(['reservation:read', 'user:reservation:read'])]
    private ?int $duration = null;

    #[ORM\Column]
    #[Groups(['reservation:read', 'user:reservation:read'])]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation:read', 'reservation:write', 'user:reservation:read'])]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation:read', 'user:reservation:read', 'reservation:read'])]
    private ?User $customer = null;

    #[ORM\ManyToOne(inversedBy: 'reservationsTroubleMaker')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation:read', 'reservation:write', 'user:reservation:read', 'reservation:read'])]
    #[Assert\Expression('value.isTroubleMaker()', 'Vous devez sélectionner un prestataire valide.')]
    private ?User $troubleMaker = null;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: Rate::class)]
    #[Groups(['user:read', 'reservation:read'])]
    private Collection $rates;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getPaymentIntentId(): ?string
    {
        return $this->paymentIntentId;
    }

    public function setPaymentIntentId(string $paymentIntentId): static
    {
        $this->paymentIntentId = $paymentIntentId;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?ReservationStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ReservationStatusEnum $status): static
    {
        $this->status = $status;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTroubleMaker(): ?User
    {
        return $this->troubleMaker;
    }

    public function setTroubleMaker(?User $troubleMaker): static
    {
        $this->troubleMaker = $troubleMaker;

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
            $rate->setReservation($this);
        }

        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === ReservationStatusEnum::ACTIVE;
    }

    public function isCanceled(): bool
    {
        return $this->status === ReservationStatusEnum::CANCELED;
    }

    public function isFinished(): bool
    {
        return $this->status === ReservationStatusEnum::FINISHED;
    }

    public function isRefunded(): bool
    {
        return $this->status === ReservationStatusEnum::REFUNDED;
    }

    public function removeRate(Rate $rate): static
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getReservation() === $this) {
                $rate->setReservation(null);
            }
        }

        return $this;
    }

    public function getRateTotalForTroubleMaker(): array
    {
        return $this->getRates()->reduce(function (array $accumulator, Rate $rate): array {
            if ($rate->isTroubleMakerRated()) {
                ++$accumulator['count'];
                $accumulator[ 'total' ] += $rate->getValue();
            }

            return $accumulator;
        }, [
            'count' => 0,
            'total' => 0
        ]);
    }
    //TODO changer customer_id par rated_id + is_trouble_maker_rated à false qd on note le customer ==> comme ça on a toujours l'id de la même concernée par la note. Donc fixer tout le reste qui en dépend ( les routes users/id/rates etc )
    public function getRateTotalForCustomer(): array
    {
        return $this->getRates()->reduce(function (array $accumulator, Rate $rate): array {
            if (!$rate->isTroubleMakerRated()) {
                ++$accumulator['count'];
                $accumulator[ 'total' ] += $rate->getValue();
            }

            return $accumulator;
        }, [
            'count' => 0,
            'total' => 0
        ]);
    }
}
