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
use App\Repository\AvailibilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvailibilityRepository::class)]
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
    normalizationContext: ['groups' => ['availability:read']],
    denormalizationContext: ['groups' => ['availability:write']],
    order: ['createdAt' => 'DESC'],
    security: '(user.isTroubleMaker() and object.getTroubleMaker() == user)
                or (user.isCompanyAdmin() and object.getTroubleMaker().getCompany() == user.getCompany()) 
                or user.isAdmin()'
)]
class Availibility implements TimestampableEntityInterface
{
    use TimestampableTrait;

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
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['availability:read', 'availability:write'])]
    #[Assert\GreaterThan(value: "now()", message: "La date de disponibilité ne peut pas être inférieure à aujourd'hui")]
    #[Assert\Expression('this.getDay() === null', 'Vous ne pouvez pas ajouter d\'heure spécifique à un jour de la semaine')]
    #[Assert\Expression('this.getTroubleMakerId() !== null', 'Vous deveez associer ce temps de travail spécifique à un prestataire.')]
    private ?\DateTimeImmutable $start_time = null;

    /*
     * end_time => dispo spécifique, liée à un troublemaker uniquement et qui correspond à un jour + un horaire précis. Donc pas de day possible avec start_time / end_time
     */
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['availability:read', 'availability:write'])]
    #[Assert\GreaterThan(propertyPath: "start_time", message: "La date de fin doit être postérieure à la date de début")]
    #[Assert\Expression('this.getDay() === null', 'Vous ne pouvez pas ajouter d\'heure spécifique à un jour de la semaine')]
    #[Assert\Expression('this.getTroubleMakerId() !== null', 'Vous deveez associer ce temps de travail spécifique à un prestataire.')]
    private ?\DateTimeImmutable $end_time = null;


    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        notInRangeMessage: "La valeur doit être entre 1 et 7",
        min: 1,
        max: 7
    )]
    #[Groups(['availability:read', 'availability:write'])]
    private ?int $day = null;

    //TODO en fait on lie ça à company pas à user + rajouter kbis ds tbale user comme ça company = établissment
    #[ORM\ManyToOne(inversedBy: 'availibilities')]
    #[Groups(['availability:read', 'availability:write'])]
    private ?User $troubleMaker = null;

    #[ORM\ManyToOne(inversedBy: 'availibilities')]
    private ?Company $company = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $company_start_time = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $company_end_time = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeImmutable $start_time): static
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeImmutable $end_time): static
    {
        $this->end_time = $end_time;

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
        return $this->company_start_time;
    }

    public function setCompanyStartTime(?string $company_start_time): static
    {
        $this->company_start_time = $company_start_time;

        return $this;
    }

    public function getCompanyEndTime(): ?string
    {
        return $this->company_end_time;
    }

    public function setCompanyEndTime(?string $company_end_time): static
    {
        $this->company_end_time = $company_end_time;

        return $this;
    }
}
