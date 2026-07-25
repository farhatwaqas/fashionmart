<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Packed = 'packed';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Packed => 'Packed',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-warning text-dark',
            self::Confirmed => 'bg-info text-dark',
            self::Packed => 'bg-primary',
            self::Shipped => 'bg-secondary',
            self::Delivered => 'bg-success',
            self::Cancelled => 'bg-danger',
        };
    }

    /** @return list<self> */
    public static function workflow(): array
    {
        return [
            self::Pending,
            self::Confirmed,
            self::Packed,
            self::Shipped,
            self::Delivered,
        ];
    }
}
