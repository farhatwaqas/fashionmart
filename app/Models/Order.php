<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'city',
        'address',
        'notes',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'discount',
        'coupon_code',
        'shipping',
        'total',
        'confirmed_at',
        'packed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'packed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function formattedTotal(): string
    {
        return 'PKR '.number_format((float) $this->total, 0);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'FC-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
        } while (static::query()->where('order_number', $number)->exists());

        return $number;
    }
}
