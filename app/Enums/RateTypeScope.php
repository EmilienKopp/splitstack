<?php

namespace App\Enums;

use Splitstack\EnumFriendly\Traits\ExtendedEnum;

enum RateTypeScope: string
{
    use ExtendedEnum;
    case Organization = 'organization';
    case Project = 'project';
    case User = 'user';
}
