<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteText extends Model
{
    protected $fillable = [
        'key_name',
        'value_ar',
        'value_en',
        'page_name',
    ];

    // Helper to get the value based on current locale
    public function getValueAttribute()
    {
        $locale = app()->getLocale(); // 'ar' or 'en'
        return $locale === 'ar' ? $this->value_ar : $this->value_en;
    }
}
