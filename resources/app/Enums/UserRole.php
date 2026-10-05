<?php

namespace App\Enums;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case TECHNICIAN = 'technician';
    case SUPER_ADMIN = 'super-admin';

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'Customer',
            self::TECHNICIAN => 'Technician',
            self::SUPER_ADMIN => 'Administrator',
        };
    }

    public function tabKey(): string
    {
        return match ($this) {
            self::CUSTOMER => 'customers',
            self::TECHNICIAN => 'technicians',
            self::SUPER_ADMIN => 'admins',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'bg-slate-900 text-[#D4AF37] border-slate-700',
            self::TECHNICIAN => 'bg-blue-100 text-blue-800 border-blue-200',
            self::CUSTOMER => 'bg-[#C5A059]/15 text-[#8F6B20] border-[#C5A059]/30',
        };
    }
}
