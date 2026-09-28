<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case PENDING = 'pending';
    case REJECT = 'reject';
    case QUOTE_SENT = 'quotesent';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::REJECT => 'Rejected',
            self::QUOTE_SENT => 'Quote Sent',
        };
    }

    /**
     * Get Tailwind badge CSS classes.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::REJECT => 'bg-rose-100 text-rose-800 border-rose-300',
            self::QUOTE_SENT => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        };
    }

    /**
     * Parse flexible/alternate status values.
     */
    public static function tryFromLoose(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        $normalized = strtolower(trim(str_replace(['-', ' '], '_', $value)));

        if ($normalized === 'quotesent' || $normalized === 'quote_sent') {
            return self::QUOTE_SENT;
        }

        if ($normalized === 'reject' || $normalized === 'rejected' || $normalized === 'declined' || $normalized === 'cancelled') {
            return self::REJECT;
        }

        if ($normalized === 'pending' || $normalized === 'in_review' || $normalized === 'new') {
            return self::PENDING;
        }

        return self::tryFrom($normalized);
    }
}
