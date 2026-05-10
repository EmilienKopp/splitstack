<?php

namespace App\Enums;

use Splitstack\EnumFriendly\Traits\ExtendedEnum;

enum RateFrequency: string
{
    use ExtendedEnum;

    case Hourly = 'hourly';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Project = 'project';
    case Fixed = 'fixed';

}
