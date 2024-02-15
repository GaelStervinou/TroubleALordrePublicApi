<?php

namespace App\Interface;

interface SoftDeleteInterface
{
    public function getDeletedAt(): ?\DateTimeImmutable;
    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self;
    public function delete(): object;

    public function isDeleted(): bool;
}