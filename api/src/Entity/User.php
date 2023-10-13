<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Enum\UserStatusEnum;
use App\Entity\Trait\SoftDeleteTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Interface\SoftDeleteInterface;
use App\Interface\TimestampableEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\State\UserPasswordHasherStateProcessor;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email')]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['user:admin:read']],
            security: 'is_granted("ROLE_ADMIN")',
            securityMessage: 'Vous n\'êtes pas autorisé à voir cette ressource.',
        ),
        new Post(
            denormalizationContext: ['groups' => ['user:create']],
            validationContext: ['groups' => ['Default', 'user:create']],
            processor: UserPasswordHasherStateProcessor::class
        ),
        new Get(
            normalizationContext: ['groups' => ['user:read']],
            security: 'is_granted("ROLE_ADMIN") or object == user && (user.isActive() == true or user.isPending() == true)',
            securityMessage: 'Vous n\'êtes pas autorisé à voir cet utilisateur.',
        ),
        new Put(processor: UserPasswordHasherStateProcessor::class),
        new Patch(processor: UserPasswordHasherStateProcessor::class),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['user:read']],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TimestampableEntityInterface, SoftDeleteInterface
{
    use TimestampableTrait;
    use SoftDeleteTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['user:me', 'user:create', 'user:update', 'user:admin:read'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;
    #[ORM\Column]
    #[Groups(['user:create', 'user:update'])]
    private ?string $password = null;

    #[Assert\Regex(
        pattern: "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/",
        message: 'Votre mot de passe doit faire 8 caractères minimum et contenir au moins une majuscule, une minuscule, un chiffre et un caractère spéciale.',
    )]
    #[Assert\NotBlank(
        message: 'Votre mot de passe ne peut pas être vide.',
        groups: ['user:create']
    )]
    #[Groups(['user:create', 'user:update'])]
    private ?string $plainPassword = null;

    #[Assert\NotBlank(
        message: 'Votre mot de passe ne peut pas être vide.',
        groups: ['user:create']
    )]
    #[Groups(['user:create', 'user:update'])]
    private ?string $verifyPassword = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 50)]
    #[NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Votre prénom doit faire 2 caractères minimum.',
        maxMessage: 'Votre prénom doit faire 50 caractères maximum.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 80)]
    #[NotBlank]
    #[Assert\Length(
        min: 2,
        max: 80,
        minMessage: 'Votre nom doit faire 2 caractères minimum.',
        maxMessage: 'Votre nom doit faire 80 caractères maximum.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update'])]
    private ?string $lastname = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $validationToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getVerifyPassword(): ?string
    {
        return $this->verifyPassword;
    }

    public function setVerifyPassword(?string $verifyPassword): User
    {
        $this->verifyPassword = $verifyPassword;
        return $this;
    }

    public function delete(): self
    {
        return $this->setStatus(UserStatusEnum::USER_STATUS_DELETED->value);
    }

    public function isDeleted(): bool
    {
        return $this->getStatus() === UserStatusEnum::USER_STATUS_DELETED->value;
    }

    public function getValidationToken(): ?string
    {
        return $this->validationToken;
    }

    public function setValidationToken(?string $validationToken): static
    {
        $this->validationToken = $validationToken;

        return $this;
    }
}