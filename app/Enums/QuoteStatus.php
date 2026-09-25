<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DECLINED = 'declined';
    case ASK_FOR_QUESTION = 'ask_for_question';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::DECLINED => 'Declined',
            self::ASK_FOR_QUESTION => 'Customer Asked Question',
        };
    }

    /**
     * Get Tailwind badge CSS classes.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::APPROVED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::DECLINED => 'bg-rose-100 text-rose-800 border-rose-300',
            self::ASK_FOR_QUESTION => 'bg-purple-100 text-purple-800 border-purple-300',
        };
    }
}
