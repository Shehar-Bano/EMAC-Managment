<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case PENDING = 'pending';
    case IN_REVIEW = 'in_review';
    case APPROVED = 'approved';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
