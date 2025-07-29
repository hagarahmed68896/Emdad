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
        'is_available',
        'business_data_id', // ✅ supplier ID

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
    public function reviews()
    {
    return $this->hasMany(Review::class);
    }

    public function supplier()
    {
    return $this->belongsTo(BusinessData::class, 'business_data_id');
    }


}

