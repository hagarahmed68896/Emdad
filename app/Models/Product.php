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
        'min_order_quantity',
        'rating',
        'reviews_count',
        'price_tiers',
        'shipping_cost',
        'estimated_delivery_days',
        'is_main_featured',
        'model_number',
        'quality',
        'is_featured',
        'is_available',
        'business_data_id',
        'preparation_days',
        'shipping_days',
        'production_capacity',
        'product_weight',
        'package_dimensions',
        'attachments',
        'material_type',
        'available_quantity',
        'sizes',   
        'colors',  
        'product_status', 

    ];

    protected $casts = [
        'images' => 'array',
        'price_tiers' => 'array',
        'sizes' => 'array',    
        'colors' => 'array',   
        'offer_expires_at' => 'datetime',
        'offer_start' => 'date',
        'offer_end' => 'date',
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

    public function offer()
    {
    return $this->hasOne(Offer::class);
    }

        public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
