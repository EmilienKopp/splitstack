<?php

namespace App\Enums;

use Splitstack\EnumFriendly\Traits\ExtendedEnum;

enum BudgetStatus: string
{
    use ExtendedEnum;

    case Draft = 'draft';
    case Approved = 'approved';
    case Active = 'active';
    case Exhausted = 'exhausted';
    case Cancelled = 'cancelled';

}
