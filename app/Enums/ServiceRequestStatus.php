<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
        };
    }

    /**
     * Get Tailwind badge CSS classes.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::ACTIVE => 'bg-blue-100 text-blue-800 border-blue-300',
            self::COMPLETED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
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

        if (in_array($normalized, ['pending', 'in_review', 'new', 'unassigned', 'reject', 'rejected', 'declined', 'cancelled'], true)) {
            return self::PENDING;
        }

        if (in_array($normalized, ['active', 'in_progress', 'processing', 'quotesent', 'quote_sent', 'quoted', 'accepted'], true)) {
            return self::ACTIVE;
        }

        if (in_array($normalized, ['completed', 'done', 'finished', 'resolved', 'closed'], true)) {
            return self::COMPLETED;
        }

        return self::tryFrom($normalized);
    }
}
