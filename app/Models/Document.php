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
        'original_name',
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($document) {
            // If original_name is empty but file_path exists
            if (empty($document->original_name) && !empty($document->file_path)) {
                $document->original_name = basename($document->file_path);
            }
        });
    }
    
public function supplier()
{
    return $this->belongsTo(User::class, 'supplier_id');
}

}
