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
        'phashes',
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
        'phashes' => 'array',
    ];

// public function getRouteKeyName()
// {
//     return 'slug';
// }


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

    public function favorites()
{
    return $this->hasMany(\App\Models\Favorite::class, 'product_id');
}



public function getPriceRangeAttribute()
{
    // Safe discount: either offer discount, product discount, or 0
    $discount = $this->offer->discount_percent ?? $this->discount_percent ?? 0;

    // Decode price tiers safely (may be null)
    $tiers = $this->price_tiers ?? [];

    // Collect all prices (base price + tiers)
    $allPrices = collect($tiers)->pluck('price')->map(fn($p) => (float)$p);
    $allPrices->push($this->price);

    // Apply discount
    $allPrices = $allPrices->map(fn($p) => $p * (1 - $discount / 100));

    return [
        'min' => $allPrices->min(),
        'max' => $allPrices->max(),
    ];
}


public function getDeliveryEstimateAttribute()
{
    $prep = $this->preparation_days ?? 0;
    $ship = $this->shipping_days ?? 0;

    return $prep + $ship; // total estimated delivery days
}



    
}
