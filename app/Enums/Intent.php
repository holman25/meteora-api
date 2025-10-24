<?php

namespace App\Enums;

enum Intent: string
{
    case WEATHER   = 'weather';
    case SMALLTALK = 'smalltalk';
    case UNKNOWN   = 'unknown';
}
