<?php

namespace App\Enum;

enum UserStatusEnum: int
{
    case USER_STATUS_BANNED = -2;
    case USER_STATUS_DELETED = -1;
    case USER_STATUS_PENDING = 0;
    case USER_STATUS_ACTIVE = 1;
}