<?php

namespace App\Enums;

enum ExecutionStatus: string
{
    case Success = 'success';
    case Failed  = 'failed';
    case Blocked = 'blocked';
}
