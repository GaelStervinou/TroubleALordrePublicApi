<?php

declare(strict_types=1);

namespace App\Enum;

enum CompanyStatusEnum: string
{
    case BANNED = "banned";
    case DELETED = "deleted";
    case PENDING = "pending";
    case ACTIVE = "active";
}