<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DECLINED = 'declined';
    case REVIEW_REQUESTED = 'review_requested';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::DECLINED => 'Declined',
            self::REVIEW_REQUESTED => 'Review Requested',
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
            self::REVIEW_REQUESTED => 'bg-blue-100 text-blue-800 border-blue-300',
        };
    }

    /**
     * Safe factory from loose/legacy string representations.
     */
    public static function tryFromLoose(?string $value): ?self
    {
        if (! $value) {
            return null;
        }

        $normalized = strtolower(trim($value));

        if (in_array($normalized, ['review_requested', 'reviewed_request', 'reviewed_requested', 'review_request', 'request_review', 'ask_for_question', 'question_asked', 'ask_question', 'question'], true)) {
            return self::REVIEW_REQUESTED;
        }

        if (in_array($normalized, ['approved', 'accept', 'accepted'], true)) {
            return self::APPROVED;
        }

        if (in_array($normalized, ['declined', 'reject', 'rejected', 'cancelled'], true)) {
            return self::DECLINED;
        }

        if (in_array($normalized, ['pending', 'quotesent', 'quote_sent'], true)) {
            return self::PENDING;
        }

        return self::tryFrom($normalized);
    }
}
