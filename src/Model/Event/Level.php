<?php

namespace App\Model\Event;

use App\Model\EnumEnhancement;

enum Level: int {
    use EnumEnhancement;

    case DEBUG = 100;
    case INFO = 200;
    case NOTICE = 250;
    case WARNING = 300;
    case ERROR = 400;
    case CRITICAL = 500;
    case ALERT = 550;
    case EMERGENCY = 600;
}
