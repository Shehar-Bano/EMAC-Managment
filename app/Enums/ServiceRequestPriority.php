<?php

namespace App\Enums;

enum ServiceRequestPriority: string
{
    case NORMAL = 'normal';
    case EMERGENCY = 'emergency';

    /**
     * Human-readable label for priority.
     */
    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'Normal',
            self::EMERGENCY => 'Emergency',
        };
    }

    /**
     * Tailwind badge classes for dashboard rendering.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::NORMAL => 'bg-slate-100 text-slate-700 border-slate-200',
            self::EMERGENCY => 'bg-rose-100 text-rose-800 border-rose-300',
        };
    }

    /**
     * Resolve priority gracefully from string or boolean is_emergency.
     */
    public static function fromInput(mixed $priority = null, mixed $isEmergency = null): self
    {
        if ($isEmergency !== null && filter_var($isEmergency, FILTER_VALIDATE_BOOLEAN)) {
            return self::EMERGENCY;
        }

        if (is_string($priority)) {
            $normalized = strtolower(trim($priority));
            if (in_array($normalized, ['emergency', 'urgent', '1', 'true'], true)) {
                return self::EMERGENCY;
            }
        }

        return self::NORMAL;
    }
}
