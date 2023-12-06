<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Trait\TimestampableTrait;
use App\Interface\TimestampableEntityInterface;
use App\Repository\UnavailibilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UnavailibilityRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(
            security: '(user.isTroubleMaker() and object.getTroubleMaker() == user)
                or (user.isCompanyAdmin() and object.getTroubleMaker().getCompany() == user.getCompany())'
        ),
        new Delete(
            security: '(user.isTroubleMaker() and object.getTroubleMaker() == user)
                or (user.isCompanyAdmin() and object.getTroubleMaker().getCompany() == user.getCompany())'
        )
    ],
    normalizationContext: ['groups' => ['unavailibility:read']],
    denormalizationContext: ['groups' => ['unavailibility:write']],
    order: ['createdAt' => 'DESC'],
    security: '(user.isTroubleMaker() and object.getTroubleMaker() == user)
                or (user.isCompanyAdmin() and object.getTroubleMaker().getCompany() == user.getCompany()) 
                or user.isAdmin()'
)]
class Unavailibility implements TimestampableEntityInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    private ?UuidInterface $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\GreaterThan(propertyPath: "startDate", message: "La date de fin doit être postérieure à la date de début")]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'unavailibilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $troubleMaker = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

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
}
