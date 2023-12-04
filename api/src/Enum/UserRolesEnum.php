<?php

declare(strict_types=1);

namespace App\Enum;

enum UserRolesEnum: string
{
    case ADMIN = 'ROLE_ADMIN';
    case COMPANY_ADMIN = 'ROLE_COMPANY_ADMIN';
    case TROUBLE_MAKER = 'ROLE_TROUBLE_MAKER';
    case USER = 'ROLE_USER';
}