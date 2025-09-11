<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialSettlement extends Model
{
    protected $fillable = [
        'supplier_id',
        'request_number',
        'amount',
        'status',
        'settlement_date',
    ];

public function supplier()
{
    return $this->belongsTo(BusinessData::class, 'supplier_id');
}

}
