<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{

    protected $fillable = [
        'product_id',
        'name',
        'user_id',
        'image',
        'description',
        'discount_percent',
        'is_offer',
        'offer_start',
        'offer_end'
    ];

       protected $casts = [
        'offer_start' => 'datetime',
        'offer_end'   => 'datetime',
    ];

      protected static function booted()
    {
        static::creating(function ($offer) {
            if (empty($offer->name) || empty($offer->image)) {
                $product = \App\Models\Product::find($offer->product_id);

                if ($product) {
                    if (empty($offer->name)) {
                        $offer->name = $product->name;
                    }
                    if (empty($offer->image)) {
                        $offer->image = $product->image;
                    }
                }
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

        // 🔹 Relation to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
