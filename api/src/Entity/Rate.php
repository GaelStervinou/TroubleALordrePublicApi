<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\BlameableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Interface\BlameableEntityInterface;
use App\Interface\TimestampableEntityInterface;
use App\Repository\RateRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    uriTemplate: '/services/{id}/rates',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['rate:read']],
            securityMessage: "Vous n'avez pas accès à cette ressource",
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'rates', fromClass: Service::class)
    ],
    order: ['createdAt' => 'DESC']
)]

#[ApiResource(
    uriTemplate: '/users/{id}/rates',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['rate:by-user:read']],
            securityMessage: "Vous n'avez pas accès à cette ressource",
            name: Rate::USER_RATES_AS_CUSTOMER_OPERATION_NAME,
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'rates', fromClass: User::class),
    ],
    order: ['createdAt' => 'DESC']
)]
#[ApiResource(
    uriTemplate: '/users/{id}/services/rates',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['rate:by-user:read']],
            securityMessage: "Vous n'avez pas accès à cette ressource",
            name: Rate::USER_RATES_AS_TROUBLE_MAKER_OPERATION_NAME,
        ),
    ],
    uriVariables: [
        'id',
    ],
    order: ['createdAt' => 'DESC']
)]
#[ORM\Entity(repositoryClass: RateRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Post(
            security: 'object.getUser() == user 
                and object.getReservation().getCustomer() == user
                and object.getReservation().isFinished()'
        )
    ],
    normalizationContext: ['groups' => ['rate:read']],
    denormalizationContext: ['groups' => ['rate:write']],
)]
class Rate implements TimestampableEntityInterface, BlameableEntityInterface
{
    use TimestampableTrait;
    use BlameableTrait;

    public const USER_RATES_AS_CUSTOMER_OPERATION_NAME = "user_rates_as_customer";
    public const USER_RATES_AS_TROUBLE_MAKER_OPERATION_NAME = "user_rates_as_trouble_maker";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['company:read', 'rate:read', 'rate:by-user:read'])]
    private ?UuidInterface $id = null;

    #[ORM\Column]
    #[Assert\Range(
        notInRangeMessage: 'La note doit être comprise entre {{ min }} et {{ max }}',
        min: 0,
        max: 5,
    )]
    #[Groups(['rate:read', 'rate:write', 'reservation:read', 'user:read', 'company:read', 'rate:by-user:read'])]
    private ?float $value = null;

    #[ORM\ManyToOne(inversedBy: 'rates')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rate:read', 'rate:write', 'reservation:read', 'user:read', 'company:read'])]
    private ?User $rated = null;

    #[ORM\ManyToOne(inversedBy: 'rates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'rates')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rate:by-user:read'])]
    private ?Service $service = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['rate:by-user:read', 'company:read'])]
    private ?string $content = null;

    #[ORM\Column]
    private ?bool $isTroubleMakerRated = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getRated(): ?User
    {
        return $this->rated;
    }

    public function setRated(?User $rated): static
    {
        $this->rated = $rated;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

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

    public function isCustomerRate(): bool
    {
        return $this->isTroubleMakerRated();
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isTroubleMakerRated(): ?bool
    {
        return $this->isTroubleMakerRated;
    }

    public function setIsTroubleMakerRated(bool $isTroubleMakerRated): static
    {
        $this->isTroubleMakerRated = $isTroubleMakerRated;

        return $this;
    }
}
