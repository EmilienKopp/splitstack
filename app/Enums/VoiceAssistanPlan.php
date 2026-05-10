<?php

namespace App\Enums;

use Splitstack\EnumFriendly\Traits\ExtendedEnum;

enum VoiceAssistanPlan: string
{
    use ExtendedEnum;

    case SECRET = 'secret';
    case FREE = 'free';
    case STANDARD = 'standard';
    case PREMIUM = 'premium';
    case ENTERPRISE = 'enterprise';
    case DISABLED = 'disabled';
}
