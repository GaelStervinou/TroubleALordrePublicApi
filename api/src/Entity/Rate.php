<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\TimestampableTrait;
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
            normalizationContext: ['groups' => ['rate:read']],
            securityMessage: "Vous n'avez pas accès à cette ressource",
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'rates', fromClass: User::class)
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
class Rate implements TimestampableEntityInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    private ?UuidInterface $id = null;

    #[ORM\Column]
    #[Assert\Range(
        notInRangeMessage: 'La note doit être comprise entre {{ min }} et {{ max }}',
        min: 0,
        max: 5,
    )]
    #[Groups(['rate:read', 'rate:write', 'reservation:read'])]
    private ?float $value = null;

    #[ORM\ManyToOne(inversedBy: 'rates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\ManyToOne(inversedBy: 'rates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'rates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

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

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

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
        return $this->getCustomer() === $this->reservation->getCustomer();
    }
}
