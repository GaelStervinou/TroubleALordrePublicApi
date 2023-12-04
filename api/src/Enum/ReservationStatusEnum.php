<?php

declare(strict_types=1);

namespace App\Enum;

enum ReservationStatusEnum: string
{
    case CANCELED = "canceled";
    case PENDING = "pending";
    case ACTIVE = "active";
    case FINISHED = "finished";
    case REFUNDED = "refunded";
}