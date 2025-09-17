<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $table = 'admin_notifications'; // new table
    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'data' => 'array',
    ];
}


