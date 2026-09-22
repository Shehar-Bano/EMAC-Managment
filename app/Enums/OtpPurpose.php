<?php

namespace App\Enums;

enum OtpPurpose: string
{
    case REGISTRATION = 'registration';
    case FORGOT_PASSWORD = 'forgot_password';
    case RESET_PASSWORD = 'reset_password';
}
