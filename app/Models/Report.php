<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reporter_id',
        'reporter_type',
        'reported_id',
        'reported_type',
        'reason',
        'report_type',
        'conversation_id'
    ];

    // Reporter (who made the report)
    public function reporter()
    {
        return $this->morphTo();
    }

    // Reported entity (who is being reported)
    public function reported()
    {
        return $this->morphTo();
    }
}

