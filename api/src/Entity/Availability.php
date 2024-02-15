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
use ApiPlatform\Metadata\Put;
use App\Entity\Trait\TimestampableTrait;
use App\Interface\TimestampableEntityInterface;
use App\Repository\AvailabilityRepository;
use App\State\CreateAndUpdateStateProcessor;
use App\State\CreateAvailabilityStateProcessor;
use App\State\UserAvailabilitiesStateProvider;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
#[UniqueEntity(fields: ['day', 'company'], message: "Vous ne pouvez renseigner qu'un horaire journalier par établissement et par jour")]
#[ApiResource(
    uriTemplate: '/companies/{id}/availabilities',
    operations: [
        new GetCollection(
            name: Availability::COMPANY_AVAILABILITIES_ROUTE_NAME,
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'availibilities', fromClass: Company::class),
    ],
    normalizationContext: ['groups' => ['availability:read']],
    order: ['createdAt' => 'DESC'],
    paginationEnabled: false
)]
#[ApiResource(
    operations: [
        new Post(
            securityPostDenormalize: "is_granted('AVAILABILITY_CREATE', object)",
            processor: CreateAndUpdateStateProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => ['availability:update']],
            securityPostDenormalize: 'user.isCompanyAdmin() and object.getTroubleMaker().getCompany().getOwner() == user',
            processor: CreateAndUpdateStateProcessor::class,
        ),
        new Delete(
            securityPostDenormalize: 'user.isCompanyAdmin() and object.getTroubleMaker().getCompany().getOwner() == user'
        )
    ],
    normalizationContext: ['groups' => ['availability:read']],
    denormalizationContext: ['groups' => ['availability:write']],
    order: ['createdAt' => 'DESC'],
)]
class Availability implements TimestampableEntityInterface
{
    use TimestampableTrait;

    public const COMPANY_AVAILABILITIES_ROUTE_NAME = 'companies_availabilities';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['availability:read'])]
    private ?UuidInterface $id = null;

    /*
     * start_time => dispo spécifique, liée à un troublemaker uniquement et qui correspond à un jour + un horaire précis. Donc pas de day possible avec start_time / end_time
     */
    #[ORM\Column(nullable: true)]
    #[Groups(['availability:read', 'availability:write', 'availability:update'])]
    #[Assert\GreaterThan(value: "now", message: "La date de disponibilité ne peut pas être inférieure à l'heure actuelle")]
    #[Assert\Expression('this.getDay() === null', 'Vous ne pouvez pas ajouter d\'heure spécifique à un jour de la semaine')]
    #[Assert\Expression('this.getTroubleMaker() !== null', 'Vous devez associer ce temps de travail spécifique à un prestataire.')]
    private ?DateTimeImmutable $startTime = null;

    /*
     * end_time => dispo spécifique, liée à un troublemaker uniquement et qui correspond à un jour + un horaire précis. Donc pas de day possible avec start_time / end_time
     */
    #[ORM\Column(nullable: true)]
    #[Groups(['availability:read', 'availability:write', 'availability:update'])]
    #[Assert\GreaterThan(propertyPath: "startTime", message: "La date de fin doit être postérieure à la date de début")]
    #[Assert\LessThan(value: "tomorrow", message: "La date de fin doit être postérieure à la date de début")]
    #[Assert\Expression('this.getDay() === null', 'Vous ne pouvez pas ajouter d\'heure spécifique à un jour de la semaine')]
    #[Assert\Expression('this.getTroubleMaker() !== null', 'Vous devez associer ce temps de travail spécifique à un prestataire.')]
    private ?DateTimeImmutable $endTime = null;


    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        notInRangeMessage: "La valeur doit être entre 1 et 7",
        min: 1,
        max: 7
    )]
    #[Groups(['availability:read', 'availability:write'])]
    private ?int $day = null;

    #[ORM\ManyToOne(inversedBy: 'availibilities')]
    #[Groups(['availability:read', 'availability:write'])]
    private ?User $troubleMaker = null;

    #[ORM\ManyToOne(inversedBy: 'availibilities')]
    #[Groups(['availability:read', 'availability:write', 'availability:update'])]
    private ?Company $company = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Groups(['availability:read', 'availability:write', 'availability:update'])]
    private ?string $companyStartTime = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Groups(['availability:read', 'availability:write'])]
    private ?string $companyEndTime = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(DateTimeImmutable $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(DateTimeImmutable $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): static
    {
        $this->day = $day;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getCompanyStartTime(): ?string
    {
        return $this->companyStartTime;
    }

    public function setCompanyStartTime(?string $companyStartTime): static
    {
        $this->companyStartTime = $companyStartTime;

        return $this;
    }

    public function getCompanyEndTime(): ?string
    {
        return $this->companyEndTime;
    }

    public function setCompanyEndTime(?string $companyEndTime): static
    {
        $this->companyEndTime = $companyEndTime;

        return $this;
    }
}
