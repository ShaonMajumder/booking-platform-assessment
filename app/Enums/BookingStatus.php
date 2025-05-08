<?php

namespace App\Enums;

class BookingStatus
{
    const PENDING = 0;
    const CONFIRMED = 1;
    const COMPLETED = 2;
    const CANCELLED = 3;

    public static function label(int $status): string
    {
        switch ($status) {
            case self::PENDING:
                return 'pending';
            case self::CONFIRMED:
                return 'confirmed';
            case self::COMPLETED:
                return 'completed';
            case self::CANCELLED:
                return 'cancelled';
            default:
                throw new \InvalidArgumentException("Invalid booking status: $status");
        }
    }

    public static function fromLabel(string $label): int
    {
        switch (strtolower($label)) {
            case 'pending':
                return self::PENDING;
            case 'confirmed':
                return self::CONFIRMED;
            case 'completed':
                return self::COMPLETED;
            case 'cancelled':
                return self::CANCELLED;
            default:
                throw new \InvalidArgumentException("Invalid booking status label: $label");
        }
    }

    // Get all possible status values
    public static function all()
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::COMPLETED,
            self::CANCELLED,
        ];
    }

    // Get all status labels
    public static function labels()
    {
        return [
            self::label(self::PENDING),
            self::label(self::CONFIRMED),
            self::label(self::COMPLETED),
            self::label(self::CANCELLED),
        ];
    }
}
