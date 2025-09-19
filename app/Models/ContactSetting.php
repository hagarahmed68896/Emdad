<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'address',
        'phone',
        'email',
        'social_links',
        'copyrights',
    ];

    protected $casts = [
        'social_links' => 'array', // JSON يتحول تلقائيًا لمصفوفة
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

