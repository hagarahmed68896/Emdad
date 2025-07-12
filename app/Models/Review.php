<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'review_date',
    ];

    // Each review belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Each review belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function likes()
{
    return $this->hasMany(ReviewLike::class);
}

public function isLikedBy(User $user): bool
{
    return $this->likes()->where('user_id', $user->id)->exists();
}

}
