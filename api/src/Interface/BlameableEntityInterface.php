<?php

namespace App\Interface;

use App\Entity\User;

interface BlameableEntityInterface
{
    public function getCreatedBy(): ?User;
    public function setCreatedBy(?User $createdBy): self;
    public function getUpdatedBy(): ?User;
    public function setUpdatedBy(?User $createdBy): self;

}