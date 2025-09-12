<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialSettlement extends Model
{
    protected $fillable = [
        'supplier_id',
        'request_number',
            'order_id',   // ✅ new column

        'amount',
        'status',
        'settlement_date',
    ];

public function supplier()
{
    return $this->belongsTo(BusinessData::class, 'supplier_id');
}
public function order()
{
    return $this->belongsTo(Order::class, 'order_id');
}
}
