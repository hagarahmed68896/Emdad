<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'payment_way',
        'status',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

       protected static function booted()
    {
        static::created(function ($order) {
            $order->order_number =  str_pad($order->id, 6, '0', STR_PAD_LEFT);
            $order->saveQuietly();
        });
    }

    public function calculateTotalAmount(): float
{
    return $this->orderItems->sum(function ($item) {
        return $item->quantity * $item->price;
    });
}

}
