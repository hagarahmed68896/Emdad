<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone_number',
        'address',
        'account_type',
        'google_id',  
        'facebook_id', 
        'provider',   
        'provider_id', 
        'profile_picture',
        'notification_settings', 
        'status', // active, inactive, banned


    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_settings' => 'array', 

        ];
    }

public function getFirstNameAttribute()
{
    $names = preg_split('/\s+/', trim($this->full_name), -1, PREG_SPLIT_NO_EMPTY);
    return $names[0] ?? '';
}

public function getLastNameAttribute()
{
    $names = preg_split('/\s+/', trim($this->full_name), -1, PREG_SPLIT_NO_EMPTY);
    return implode(' ', array_slice($names, 1));
}
    public function cart()
    {
        return $this->hasOne(Cart::class)->where('status', 'active');
      
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function reviews()
{
    return $this->hasMany(Review::class);
}

public function likedReviews()
{
    return $this->belongsToMany(Review::class, 'review_likes')->withTimestamps();
}



    /**
     * Check if a product is favorited by the user.
     *
     * @param int $productId
     * @return bool
     */
    public function hasFavorited($productId)
    {
        return $this->favorites()->where('product_id', $productId)->exists();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // داخل User.php
    // public function business()
    // {
    // return $this->hasOne(BusinessData::class);
    // }
public function business()
{
    return $this->hasOne(BusinessData::class, 'user_id', 'id');
}

public function offers()
{
    return $this->hasMany(Offer::class);
}

//reports relationship
public function reportsMade()
{
    return $this->morphMany(Report::class, 'reporter');
}

public function reportsReceived()
{
    return $this->morphMany(Report::class, 'reported');
}

    public function blocks()
    {
        return $this->hasMany(UserBlock::class, 'blocker_id');
    }

    // Users that blocked this user
    public function blockedBy()
    {
        return $this->hasMany(UserBlock::class, 'blocked_id');
    }

}
