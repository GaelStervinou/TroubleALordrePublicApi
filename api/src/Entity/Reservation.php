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
use App\Controller\Action\PaymentIntent\CreatePaymentIntentAction;
use App\Entity\Trait\TimestampableTrait;
use App\Enum\ReservationStatusEnum;
use App\Interface\TimestampableEntityInterface;
use App\Repository\ReservationRepository;
use App\State\Reservation\CreateReservationSateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    uriTemplate: '/reservations/payment-intent',
    operations: [
        new Post(
            controller: CreatePaymentIntentAction::class,
            normalizationContext: ['groups' => ['paymentIntent:read']],
            denormalizationContext: ['groups' => ['paymentIntent:write']],
            security: 'user.isUser()',
            output: PaymentIntent::class,
            name: 'payment-intent',
        ),
    ]
)]
#[ApiResource(
    uriTemplate: '/users/{id}/reservations',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['reservation:read']],
            security: 'user.isAdmin or id == user.getId()',
            securityMessage: "Vous n'avez pas accès à cette ressource",
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
            normalizationContext: ['groups' => ['reservation:read']],
            security: 'user.isAdmin()
                        or (id == user.getId() and user.isTroubleMaker())
                        or (user.isCompanyAdmin() and id == user.getCompany().getId())',
            securityMessage: "Vous n'avez pas accès à cette ressource",
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
            security: '(user.isTroubleMaker() and object.getTroubleMaker() == user)
                or (user.isCompanyAdmin() and object.getTroubleMaker().getCompany() == user.getCompany())
                or user == object.getCustomer()'
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

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
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
    #[Groups(['reservation:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\GreaterThan('today', message: "La date ne peut pas être antérieure à aujourd'hui")]
    #[Groups(['reservation:write', 'reservation:read'])]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 50, options: ['default' => ReservationStatusEnum::PENDING])]
    #[Assert\Choice(
        choices: [
            ReservationStatusEnum::ACTIVE,
            ReservationStatusEnum::PENDING,
            ReservationStatusEnum::CANCELED,
            ReservationStatusEnum::FINISHED,
            ReservationStatusEnum::REFUNDED,
        ],
        message: "Le status n'est pas valide"
    )]
    #[Groups(['reservation:update'])]
    private ?ReservationStatusEnum $status = null;

    #[ORM\Column()]
    #[Groups(['reservation:read'])]
    private ?int $duration = null;

    #[ORM\Column]
    #[Groups(['reservation:read'])]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation:read'])]
    private ?User $customer = null;

    #[ORM\ManyToOne(inversedBy: 'reservationsTroubleMaker')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?User $troubleMaker = null;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: Rate::class)]
    #[Groups(['reservation:read', 'user:read'])]
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

    public function isPending(): bool
    {
        return $this->status === ReservationStatusEnum::PENDING;
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

    public function getRateTotalForTroubleMaker(string $userId): array
    {
        return $this->getRates()->reduce(function (array $accumulator, Rate $rate) use ($userId): array {
            if ($userId !== $rate->getCustomer()?->getId()) {
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
