<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill extends Model
{
    protected $fillable = [
        'bill_number',
        'user_id',
        'order_id',
        'payment_way',
        'total_price',
        'status',
    ];

    // ✅ ربط المستخدم
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ✅ ربط الطلب (لو احتجت ربطه مباشرة)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }
}
