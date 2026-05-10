<?php

namespace App\Enums;

use Splitstack\EnumFriendly\Traits\ExtendedEnum;

enum TaskPriority: int
{
    use ExtendedEnum;
    case None = 0;
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Urgent = 4;
    case Critical = 5;
    case LifeThreatening = 6;
    case Blocker = -1;
}
