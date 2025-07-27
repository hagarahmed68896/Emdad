<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_name', 'quantity', 'unit_price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
{
    return $this->belongsTo(Product::class);
}

    public function getLineTotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    protected static function booted()
{
    static::saved(function ($item) {
        $order = $item->order;
        $order->total_amount = $order->calculateTotalAmount();
        $order->saveQuietly();
    });

    static::deleted(function ($item) {
        $order = $item->order;
        $order->total_amount = $order->calculateTotalAmount();
        $order->saveQuietly();
    });
}

}
