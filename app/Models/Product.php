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
        'sub_category_id',
        'images', 
        'is_offer',
        'discount_percent',
        'offer_expires_at',
        'supplier_name',
        'supplier_confirmed', 
        'color', 
        'size', 
        'gender',
        'material'
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
        'color' => 'array',  // Cast 'color' to array
        'size' => 'array',   // Cast 'size' to array
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

}