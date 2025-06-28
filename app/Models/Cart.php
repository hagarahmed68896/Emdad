<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'status',
    ];

    /**
     * Get the user that owns the cart.
     * A cart can belong to a user (if logged in).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items for the cart.
     * A cart has many cart items.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the total price of all items in the cart.
     * This is a convenient accessor.
     */
    public function getTotalPriceAttribute()
    {
        return $this->items->map(function($item) {
            return $item->quantity * $item->price_at_addition;
        })->sum();
    }

    /**
     * Get the total number of items (total quantity) in the cart.
     * This is a convenient accessor.
     */
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get the count of unique products in the cart.
     * This is a convenient accessor.
     */
    public function getUniqueProductCountAttribute()
    {
        return $this->items->count();
    }
}