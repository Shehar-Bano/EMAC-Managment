<?php

namespace App\Enums;

enum AccountStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case SUSPENDED = 'suspended';
    case BLOCKED = 'blocked';
    case DELETED = 'deleted';
}
