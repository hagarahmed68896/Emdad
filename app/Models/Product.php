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
        'image',
        'images',
        'sub_category_id',
        'is_offer',
        'discount_percent',
        'offer_expires_at',
        'supplier_name',
        'supplier_confirmed',
        'min_order_quantity',
        'rating',
        'reviews_count',
        'price_tiers',
        'shipping_cost',
        'estimated_delivery_days',
        'is_main_featured',
        'model_number',
        'quality',
        'specifications',
        'is_featured',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images' => 'array',
        'price_tiers' => 'array',
        'specifications' => 'array',
        'offer_expires_at' => 'datetime',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}
