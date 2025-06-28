<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price_at_addition',
        'options', // For product variations like size, color
    ];

    /**
     * The attributes that should be cast.
     * This is crucial for the 'options' JSON column.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Get the cart that the item belongs to.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product associated with the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class); // Make sure you have a Product model
    }

    /**
     * Get the subtotal for this specific cart item.
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price_at_addition;
    }
}