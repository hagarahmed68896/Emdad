<?php

// app/Models/Faq.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faq extends Model
{
    protected $fillable = ['question', 'answer', 'type', 'user_type'];

    public $incrementing = false; // because UUID is not auto-increment
    protected $keyType = 'string';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}


