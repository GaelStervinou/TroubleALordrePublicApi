<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Enum\UserRolesEnum;
use App\Enum\UserStatusEnum;
use App\Entity\Trait\SoftDeleteTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Interface\SoftDeleteInterface;
use App\Interface\TimestampableEntityInterface;
use App\State\User\UserMeProvider;
use App\State\UserAvailabilitiesStateProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\State\UserPasswordHasherStateProcessor;
use Ramsey\Uuid\UuidInterface;
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
        ),
        new Get(
            uriTemplate: '/me',
            normalizationContext: ['groups' => ['user:read']],
            security: 'object == user && (user.isActive() == true or user.isPending() == true)',
            securityMessage: 'Votre compte n\'est plus disponible.',
            provider: UserMeProvider::class,
        ),
        new Put(processor: UserPasswordHasherStateProcessor::class),
        new Patch(processor: UserPasswordHasherStateProcessor::class)
    ],
    normalizationContext: ['groups' => ['user:read']],
    //TODO denormalizationContext à faire !!!!
)]
#[ApiResource(
    uriTemplate: '/companies/{id}/users',
    operations: [
        new GetCollection(
            uriVariables: [
                'id' => new Link(fromProperty: 'users', fromClass: Company::class)
            ],
            normalizationContext: ['groups' => ['user:read']],
        ),
    ],

    order: ['createdAt' => 'DESC']
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TimestampableEntityInterface, SoftDeleteInterface
{
    use TimestampableTrait;
    use SoftDeleteTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['company:read', 'user:read', 'company:admin:read', 'rate:by-user:read', 'company:dashboard:read', 'invitation:read', 'reservation:read', 'user:admin:read', 'reservation:read'])]
    private ?UuidInterface $id = null;
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['user:me:read', 'user:create', 'user:update', 'user:admin:read', 'invitation:read'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\Regex(
        pattern: "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/",
        message: 'Votre mot de passe doit faire 8 caractères minimum et contenir au moins une majuscule, une minuscule, un chiffre et un caractère spéciale.',
    )]
    #[Assert\NotBlank(
        message: 'Votre mot de passe ne peut pas être vide.',
        groups: ['user:create', 'user:reset-password']
    )]
    #[Groups(['user:create', 'user:update', 'user:reset-password'])]
    private ?string $plainPassword = null;

    #[Assert\NotBlank(
        message: 'Votre mot de passe ne peut pas être vide.',
        groups: ['user:create', 'user:reset-password']
    )]
    #[Groups(['user:create', 'user:update', 'user:reset-password'])]
    private ?string $verifyPassword = null;

    //TODO vérifeir que si c pas un admin il peut chanegr son role que pour troublemaker
    #[ORM\Column(type: 'json')]
    #[Groups(['user:read', 'reservation:read', 'user:admin:read'])]
    private array $roles = [];

    #[ORM\Column(length: 50)]
    #[NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Votre prénom doit faire 2 caractères minimum.',
        maxMessage: 'Votre prénom doit faire 50 caractères maximum.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update', 'reservation:read', 'company:read', 'company:admin:read', 'rate:by-user:read', 'company:dashboard:read', 'user:reservation:read', 'invitation:read', 'user:admin:read', 'reservation:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 80)]
    #[NotBlank]
    #[Assert\Length(
        min: 2,
        max: 80,
        minMessage: 'Votre nom doit faire 2 caractères minimum.',
        maxMessage: 'Votre nom doit faire 80 caractères maximum.',
    )]
    #[Groups(['user:read', 'user:create', 'user:update', 'reservation:read', 'company:read', 'company:admin:read', 'rate:by-user:read', 'company:dashboard:read', 'user:reservation:read', 'invitation:read', 'user:admin:read', 'reservation:read'])]
    private ?string $lastname = null;

    #[ORM\Column (options: ['default' => UserStatusEnum::USER_STATUS_PENDING])]
    #[Groups(['user:read', 'user:admin:read'])]
    private ?UserStatusEnum $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $validationToken = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Invitation::class)]
    private Collection $invitations;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'troubleMaker', targetEntity: Reservation::class)]
    private Collection $reservationsTroubleMaker;

    #[ORM\OneToMany(mappedBy: 'rated', targetEntity: Rate::class)]
    private Collection $rates;

    #[ORM\OneToMany(mappedBy: 'troubleMaker', targetEntity: Unavailability::class)]
    private Collection $unavailibilities;

    #[ORM\OneToMany(mappedBy: 'troubleMaker', targetEntity: Availability::class)]
    private Collection $availibilities;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Company $company = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Assert\Length(
        min: 5,
        max: 5,
        minMessage: "Le kbis invalide",
        maxMessage: "Le kbis invalide"
    )]
    #[Groups(['user:read', 'company:read', 'company:admin:read', 'company:dashboard:read'])]
    private ?string $kbis = null;

    #[ORM\ManyToOne]
    #[Groups(['company:read', 'rate:by-user:read', 'company:dashboard:read', 'user:read', 'invitation:read', 'reservation:read', 'reservation:read'])]
    private ?Media $profilePicture = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Company::class)]
    private Collection $ownedCompanies;
    #[Groups(['company:dashboard:read'])]
    private int $currentMonthTotalReservations = 0;

    public function __construct()
    {
        $this->invitations = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reservationsTroubleMaker = new ArrayCollection();
        $this->rates = new ArrayCollection();
        $this->unavailibilities = new ArrayCollection();
        $this->availibilities = new ArrayCollection();
        $this->ownedCompanies = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
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

    public function getStatus(): UserStatusEnum
    {
        return $this->status;
    }

    public function setStatus(UserStatusEnum $status): static
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
        return $this->setStatus(UserStatusEnum::USER_STATUS_DELETED);
    }

    public function isDeleted(): bool
    {
        return $this->getStatus() === UserStatusEnum::USER_STATUS_DELETED;
    }

    public function isPending(): bool
    {
        return $this->getStatus() === UserStatusEnum::USER_STATUS_PENDING;
    }

    public function isActive(): bool
    {
        return $this->getStatus() === UserStatusEnum::USER_STATUS_ACTIVE;
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

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): static
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): static
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setReceiver($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getReceiver() === $this) {
                $invitation->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setCustomer($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getCustomer() === $this) {
                $reservation->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservationsTroubleMaker(): Collection
    {
        return $this->reservationsTroubleMaker;
    }

    public function addReservationsTroubleMaker(Reservation $reservationsTroubleMaker): static
    {
        if (!$this->reservationsTroubleMaker->contains($reservationsTroubleMaker)) {
            $this->reservationsTroubleMaker->add($reservationsTroubleMaker);
            $reservationsTroubleMaker->setTroubleMaker($this);
        }

        return $this;
    }

    public function removeReservationsTroubleMaker(Reservation $reservationsTroubleMaker): static
    {
        if ($this->reservationsTroubleMaker->removeElement($reservationsTroubleMaker)) {
            // set the owning side to null (unless already changed)
            if ($reservationsTroubleMaker->getTroubleMaker() === $this) {
                $reservationsTroubleMaker->setTroubleMaker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rate>
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    public function addRate(Rate $rate): static
    {
        if (!$this->rates->contains($rate)) {
            $this->rates->add($rate);
            $rate->setRated($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): static
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getRated() === $this) {
                $rate->setRated(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Unavailability>
     */
    public function getUnavailibilities(): Collection
    {
        return $this->unavailibilities;
    }

    public function addUnavailibility(Unavailability $unavailibility): static
    {
        if (!$this->unavailibilities->contains($unavailibility)) {
            $this->unavailibilities->add($unavailibility);
            $unavailibility->setTroubleMaker($this);
        }

        return $this;
    }

    public function removeUnavailibility(Unavailability $unavailibility): static
    {
        if ($this->unavailibilities->removeElement($unavailibility)) {
            // set the owning side to null (unless already changed)
            if ($unavailibility->getTroubleMaker() === $this) {
                $unavailibility->setTroubleMaker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getAvailibilities(): Collection
    {
        return $this->availibilities;
    }

    public function addAvailibility(Availability $availibility): static
    {
        if (!$this->availibilities->contains($availibility)) {
            $this->availibilities->add($availibility);
            $availibility->setTroubleMaker($this);
        }

        return $this;
    }

    public function removeAvailibility(Availability $availibility): static
    {
        if ($this->availibilities->removeElement($availibility)) {
            // set the owning side to null (unless already changed)
            if ($availibility->getTroubleMaker() === $this) {
                $availibility->setTroubleMaker(null);
            }
        }

        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array(UserRolesEnum::ADMIN->value, $this->getRoles(), true);
    }

    public function isUser(): bool
    {
        return in_array('ROLE_USER', $this->getRoles(), true);
    }

    public function isTroubleMaker(): bool
    {
        return in_array(UserRolesEnum::TROUBLE_MAKER->value, $this->getRoles(), true);
    }

    public function isCompanyAdmin(): bool
    {
        return in_array(UserRolesEnum::COMPANY_ADMIN->value, $this->getRoles(), true);
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

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(?string $kbis): static
    {
        $this->kbis = $kbis;

        return $this;
    }

    #[Groups(['user:read'])]
    public function getTroubleMakerAverageRate(): ?float
    {
        if (!$this->isTroubleMaker()) {
            return null;
        }
        $userRates = $this->getTroubleMakerRatesTotalAndCount();
        if (0 === $userRates['count']) {
            return null;
        }
        return $userRates['total'] / $userRates['count'];
    }

    private function getTroubleMakerRatesTotalAndCount(): array
    {
        return $this->getReservationsTroubleMaker()->reduce(function (array $accumulator, Reservation $reservation): array {
            $reservationRates = $reservation->getRateTotalForTroubleMaker();
            $accumulator[ 'count' ] += $reservationRates['count'];
            $accumulator[ 'total' ] += $reservationRates[ 'total' ];
            return $accumulator;
        }, ['count' => 0, 'total' => 0]);
    }

    public function getProfilePicture(): ?Media
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?Media $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getOwnedCompanies(): Collection
    {
        return $this->ownedCompanies;
    }

    public function addOwnedCompany(Company $ownedCompany): static
    {
        if (!$this->ownedCompanies->contains($ownedCompany)) {
            $this->ownedCompanies->add($ownedCompany);
            $ownedCompany->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedCompany(Company $ownedCompany): static
    {
        if ($this->ownedCompanies->removeElement($ownedCompany)) {
            // set the owning side to null (unless already changed)
            if ($ownedCompany->getOwner() === $this) {
                $ownedCompany->setOwner(null);
            }
        }

        return $this;
    }

    public function getCurrentMonthTotalReservations(): int
    {
        return $this->currentMonthTotalReservations;
    }

    public function setCurrentMonthTotalReservations(int $currentMonthTotalReservations): self
    {
        $this->currentMonthTotalReservations = $currentMonthTotalReservations;
        return $this;
    }


    private function getCustomerRatesTotalAndCount(): array
    {
        return $this->getReservations()->reduce(function (array $accumulator, Reservation $reservation): array {
            $reservationRates = $reservation->getRateTotalForCustomer();
            $accumulator[ 'count' ] += $reservationRates['count'];
            $accumulator[ 'total' ] += $reservationRates[ 'total' ];
            return $accumulator;
        }, ['count' => 0, 'total' => 0]);
    }

    #[Groups(['user:read'])]
    public function getUserAverageRatesValue(): ?float
    {
        $userCustomerRates = $this->getCustomerRatesTotalAndCount();
        $userTroubleMakerRates = $this->getTroubleMakerRatesTotalAndCount();
        if (0 === $userCustomerRates['count'] && 0 === $userTroubleMakerRates['count']) {
            return null;
        }
        $receivedRatesCount = $userCustomerRates['count'] + $userTroubleMakerRates['count'];
        return ($userCustomerRates['total'] + $userTroubleMakerRates['total']) / ($receivedRatesCount);
    }

    #[Groups(['user:read'])]
    public function getUserReservationsAsCustomerCount(): int
    {
        return $this->getReservations()->count();
    }

    #[Groups(['user:read'])]
    public function getUserReservationsAsTroubleMakerCount(): int
    {
        return $this->getReservationsTroubleMaker()->count();
    }

    #[Groups(['user:read'])]
    public function getRatesReceivedCount(): int
    {
        $userCustomerRates = $this->getCustomerRatesTotalAndCount();
        $userTroubleMakerRates = $this->getTroubleMakerRatesTotalAndCount();
        return $userCustomerRates['count'] + $userTroubleMakerRates['count'];
    }
}