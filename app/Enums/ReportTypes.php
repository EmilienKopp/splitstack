<?php

namespace App\Enums;

use Splitstack\EnumFriendly\Traits\ExtendedEnum;

enum ReportTypes: string
{
    use ExtendedEnum;

    case TECHNICAL = 'technical';
    case FINANCIAL = 'financial';
    case OPERATIONAL = 'operational';
    case TASK_BASED = 'task_based';

}
