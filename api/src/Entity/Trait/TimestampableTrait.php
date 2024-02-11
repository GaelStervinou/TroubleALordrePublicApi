<?php

namespace App\Entity\Trait;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait TimestampableTrait
{
    #[ORM\Column]
    #[Groups(['company:read', 'rate:by-user:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;


    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $date): self
    {
        $this->createdAt = $date;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $date): self
    {
        $this->updatedAt = $date;
        return $this;
    }
}