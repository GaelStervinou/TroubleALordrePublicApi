<?php

declare(strict_types=1);

namespace App\Enum;

enum InvitationStatusEnum: string
{
    case REFUSED = "refused";
    case CANCELED = "canceled";
    case PENDING = "pending";
    case ACCEPTED = "accepted";
}