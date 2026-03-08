<?php

namespace App\Enum;

enum DateRange: string
{
    case LAST_MONTH = 'last_month';
    case QUARTER = 'quarter';
    case YEAR = 'year';
}
