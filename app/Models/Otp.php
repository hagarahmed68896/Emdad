<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otp extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'code',
        'identifier_type',
        'identifier',
        'expires_at',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
