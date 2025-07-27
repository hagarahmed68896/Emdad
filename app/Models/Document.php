<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'document_name',
        'supplier_id',
        'file_path',   // ✅ أضف هذا
        'status',
        'notes',
    ];


// public function businessData(): BelongsTo
// {
//     return $this->belongsTo(BusinessData::class, 'supplier_id', 'company_name');
// }

// public function supplier()
// {
//     return $this->belongsTo(BusinessData::class, 'supplier_id');
// }
public function supplier()
{
    return $this->belongsTo(User::class, 'supplier_id');
}

}
