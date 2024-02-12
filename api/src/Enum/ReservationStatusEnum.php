<?php

declare(strict_types=1);

namespace App\Enum;

enum ReservationStatusEnum: string
{
    case CANCELED = "canceled";
    case ACTIVE = "active";
    case FINISHED = "finished";
    case REFUNDED = "refunded";
}