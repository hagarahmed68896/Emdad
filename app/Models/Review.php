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
        'order_id',      // ⬅️ added
        'rating',
        'comment',
        'issues',        // ⬅️ added
        'issue_type',    // ⬅️ added
        'status',        // ⬅️ added
        'review_date',
        'is_complaint',
    ];

    protected $casts = [
        'issues' => 'array',
        'review_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
{
    return $this->belongsTo(Order::class);
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

