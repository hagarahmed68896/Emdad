<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        // 'image', // You might want to add 'image' here if it's directly fillable
        // You'll likely need to add other attributes from your seeder here if they're not already in fillable
        // e.g., 'is_offer', 'discount_percent', 'offer_expires_at', 'supplier_name', 'supplier_confirmed', 'min_order_quantity', 'rating'
        'category_id',
        'images', // <--- This attribute should be in fillable if you mass assign it
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images' => 'array', // <--- This is the correct place for the cast!
        'offer_expires_at' => 'datetime', // Assuming you have an 'offer_expires_at' column as well
        // Add other casts if applicable, like 'supplier_confirmed' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}