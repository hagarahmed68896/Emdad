<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name',    'name_en',
 'slug','iconUrl','description'];
    // public function getNameAttribute()
    // {
    //     return app()->getLocale() === 'ar' ? $this->name : $this->name;
    // }
   public function products(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
             Product::class,      // The final model we want to reach (products)
            SubCategory::class,  // The intermediate model (sub_categories)
            'category_id',       // Foreign key on the intermediate model (sub_categories table)
                                 // that links to the categories table (this model's ID).
            'sub_category_id',   // Foreign key on the final model (products table)
                                 // that links to the sub_categories table.
            'id',                // Local key on the current model (categories table)
                                 // that the intermediate model references.
            'id'                 // Local key on the intermediate model (sub_categories table)
                                 // that the final model references.
        );
    }
    public function subCategories(): HasMany
{
    return $this->hasMany(SubCategory::class, 'category_id');
}
}
