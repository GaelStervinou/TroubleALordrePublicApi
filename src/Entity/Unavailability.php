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
use App\Repository\UnavailabilityRepository;
use App\State\CreateAndUpdateStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UnavailabilityRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            securityMessage: 'Vous ne pouvez pas ajouter d\'indisponibilité à cet utilisateur car il ne fait pas partie de votre établissemnt',
            securityPostDenormalize: 'object.getTroubleMaker().getCompany().getOwner() == user',
            processor: CreateAndUpdateStateProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => ['unavailibility:update']],
            securityMessage: 'Vous ne pouvez pas modifier d\'indisponibilité pour cet utilisateur car il ne fait pas partie de votre établissemnt',
            securityPostDenormalize: 'object.getTroubleMaker().getCompany().getOwner() == user',
            processor: CreateAndUpdateStateProcessor::class,
        ),
        new Delete(
            securityMessage: 'Vous ne pouvez pas supprimer d\'indisponibilité pour cet utilisateur car il ne fait pas partie de votre établissemnt',
            securityPostDenormalize: 'object.getTroubleMaker().getCompany().getOwner() == user'
        )
    ],
    normalizationContext: ['groups' => ['unavailibility:read']],
    denormalizationContext: ['groups' => ['unavailibility:write']],
    order: ['createdAt' => 'DESC'],
)]
class Unavailability implements TimestampableEntityInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['unavailibility:read'])]
    private ?UuidInterface $id = null;

    #[ORM\Column()]
    #[Assert\GreaterThan(value: "now", message: "La date d'indisponibilité ne peut pas être inférieure à l'heure actuelle")]
    #[Groups(['unavailibility:write', 'unavailibility:update'])]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column()]
    #[Assert\GreaterThan(propertyPath: "startTime", message: "La date de fin doit être postérieure à la date de début")]
    #[Assert\Expression('this.isValidEndTime()', 'Vous ne pouvez mettre une indisponibilité que sur un seul jour.')]
    #[Groups(['unavailibility:write', 'unavailibility:update'])]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\ManyToOne(inversedBy: 'unavailibilities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['unavailibility:write'])]
    private ?User $troubleMaker = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeImmutable $endTime): static
    {
        $this->endTime = $endTime;

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

    public function isValidEndTime(): bool
    {
        return $this->getStartTime()->setTime(0, 0)->add(new \DateInterval("P1D")) > $this->getEndTime();
    }
}
