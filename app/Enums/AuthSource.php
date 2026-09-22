<?php

namespace App\Enums;

enum AuthSource: string
{
    case EMAIL = 'email';
    case GOOGLE = 'google';
    case APPLE = 'apple';
    case FACEBOOK = 'facebook';
}
