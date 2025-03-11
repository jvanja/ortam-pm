<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Canceled = 'canceled';
}
