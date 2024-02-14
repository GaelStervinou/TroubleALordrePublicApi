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
use App\Entity\Trait\SoftDeleteTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Enum\InvitationStatusEnum;
use App\Interface\SoftDeleteInterface;
use App\Interface\TimestampableEntityInterface;
use App\Repository\InvitationRepository;
use App\State\CreateAndUpdateInvitationStateProcessor;
use App\State\UpdateInvitationStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\Choice;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/users/{id}/invitations',
            uriVariables: [
                'id' => new Link(fromProperty: 'invitations', fromClass: User::class)
            ],
            normalizationContext: ['groups' => ['invitation:read']],
            security: 'user.isAdmin()',
        ),
        new GetCollection(
            uriTemplate: '/users/my-invitations',
            normalizationContext: ['groups' => ['invitation:read']],
            security: 'user.isTroubleMaker()',
            name: Invitation::MY_INVITATIONS_ROUTE_NAME,
        ),
    ],
    order: ['createdAt' => 'DESC'],
)]
#[ApiResource(
    uriTemplate: '/companies/{id}/invitations',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['invitation:read']],
            name: self::COMPANY_INVITATIONS_ROUTE_NAME,
        ),
    ],
    uriVariables: [
        'id' => new Link(fromProperty: 'invitations', fromClass: Company::class)
    ],
    order: ['createdAt' => 'DESC']
)]
#[ORM\Entity(repositoryClass: InvitationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'user.isAdmin()'
        ),
        new Get(
            security: 'user.isAdmin() 
                or (user.isCompanyAdmin() and object.getCompany() == user.getCompany())
                or (object.getReceiver() == user)'
        ),
        new Post(
            securityMessage: "Vous ne pouvez pas créer d'invitations",
            securityPostDenormalize: '(user.isAdmin()
                or (object.getCompany().getOwner() === user))
                and object.getCompany().isActive()',
            processor: CreateAndUpdateInvitationStateProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => ['invitation:update']],
            security: 'object.getReceiver() == user or object.getCompany().getOwner() == user',
            processor: UpdateInvitationStateProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => ['invitation:read']],
    denormalizationContext: ['groups' => ['invitation:write']],
    order: ['createdAt' => 'DESC'],
)]
class Invitation implements TimestampableEntityInterface, SoftDeleteInterface
{
    use TimestampableTrait;
    use SoftDeleteTrait;
    public const MY_INVITATIONS_ROUTE_NAME = 'users_my_invitations';
    public const COMPANY_INVITATIONS_ROUTE_NAME = 'companies_invitations';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['invitation:read'])]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'invitations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invitation:read', 'invitation:write'])]
    private ?User $receiver = null;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invitation:read', 'invitation:write'])]
    private ?Company $company = null;

    #[ORM\Column(length: 10, options: ['default' => InvitationStatusEnum::PENDING->value])]
    #[Choice(
        choices: [
            InvitationStatusEnum::PENDING->value,
            InvitationStatusEnum::REFUSED->value,
            InvitationStatusEnum::ACCEPTED->value,
            InvitationStatusEnum::CANCELED->value,
        ],
        message: "Le statut n'est pas valide"
    )]
    #[Groups(['invitation:read', 'invitation:update'])]
    private ?string $status = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function delete(): self
    {
        return $this->setStatus(InvitationStatusEnum::CANCELED->value);
    }

    public function isDeleted(): bool
    {
        return InvitationStatusEnum::CANCELED->value === $this->getStatus();
    }

    public function isPending(): bool
    {
        return InvitationStatusEnum::PENDING->value === $this->getStatus();
    }
}
