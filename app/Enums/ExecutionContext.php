<?php

namespace App\Enums;

use Splitstack\EnumFriendly\Traits\ExtendedEnum;

enum ExecutionContext: string
{
    use ExtendedEnum;

    case LOCAL = 'local';
    case WEB = 'web';
    case CLI = 'cli';
    case API = 'api';
}
