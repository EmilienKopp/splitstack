<?php

namespace App\Enums;

use Splitstack\EnumFriendly\Traits\ExtendedEnum;

enum RateType: string
{
    use ExtendedEnum;

    case Standard = 'standard';
    case Overtime = 'overtime';
    case Holiday = 'holiday';
    case Special = 'special';
    case Custom = 'custom';
}
