<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo for relationships
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany for products

class SubCategory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sub_categories'; // Explicitly define table name for clarity

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_en',   // ✅ added
        'slug',
        'iconUrl',
        'category_id', // Ensure category_id is fillable as it's a foreign key
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No specific casts needed for these columns by default,
        // but you would add them here if 'iconUrl' was a JSON object, etc.
    ];

    /**
     * Get the category that owns the sub-category.
     * This defines the inverse of the one-to-many relationship with Category.
     */
    public function category(): BelongsTo
    {
        // Laravel will automatically look for 'category_id' on the 'sub_categories' table
        // and link to the 'id' of the 'categories' table.
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the products for the sub-category.
     * This defines a one-to-many relationship with Products.
     */
    public function products(): HasMany
    {
        // 'sub_category_id' is specified here because it's the foreign key
        // on the 'products' table that links back to this 'sub_categories' table.
        return $this->hasMany(Product::class, 'sub_category_id');
    }
}