<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case InPreparation = 'in_preparation';
    case Sent = 'sent';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
