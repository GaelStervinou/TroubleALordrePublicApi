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
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    uriTemplate: '/categories/{id}/services',
    operations: [
        new GetCollection(),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'services', fromClass: Category::class)
    ],
    order: ['createdAt' => 'DESC']
)]
#[UniqueEntity(fields: ['name'], message: 'Cette catégorie existe déjà')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            security: 'user.isAdmin()'
        ),
        new Patch(
            security: 'user.isAdmin()'
        ),
        new Delete(
            security: 'user.isAdmin()'
        )
    ],
    normalizationContext: ['groups' => ['category:read']],
    denormalizationContext: ['groups' => ['category:write']],
    order: ['createdAt' => 'DESC'],
)]
class Category implements TimestampableEntityInterface
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
    #[Groups(['category:read', 'category:write', 'service:read', 'reservation:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Service::class)]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: RateType::class)]
    #[Groups(['category:read'])]
    private Collection $rateTypes;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->rateTypes = new ArrayCollection();
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
            $service->setCategory($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getCategory() === $this) {
                $service->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RateType>
     */
    public function getRateTypes(): Collection
    {
        return $this->rateTypes;
    }

    public function addRateType(RateType $rateType): static
    {
        if (!$this->rateTypes->contains($rateType)) {
            $this->rateTypes->add($rateType);
            $rateType->setCategory($this);
        }

        return $this;
    }

    public function removeRateType(RateType $rateType): static
    {
        if ($this->rateTypes->removeElement($rateType)) {
            // set the owning side to null (unless already changed)
            if ($rateType->getCategory() === $this) {
                $rateType->setCategory(null);
            }
        }

        return $this;
    }
}
