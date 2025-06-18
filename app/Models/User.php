<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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


}
