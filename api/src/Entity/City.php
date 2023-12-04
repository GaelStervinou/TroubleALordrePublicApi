<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Trait\TimestampableTrait;
use App\Interface\TimestampableEntityInterface;
use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            security: 'user.isAdmin()'
        ),
        new Put(
            security: 'user.isAdmin()'
        ),
        new Delete(
            security: 'user.isAdmin()'
        )
    ],
    normalizationContext: ['groups' => ['city:read']],
    denormalizationContext: ['groups' => ['city:write']],
    order: ['createdAt' => 'DESC'],
)]class City implements TimestampableEntityInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\Length(
        min: 5,
        max: 30,
        minMessage: "Le nom doit avoir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'cities')]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
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
            $service->addCity($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            $service->removeCity($this);
        }

        return $this;
    }
}
