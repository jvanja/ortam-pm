<?php

namespace App\Enums;

enum Task: string
{
    case Visit = 'visit';
    case Research = 'research';
    case Fieldwork = 'fieldwork';
    case Report = 'report';
}
