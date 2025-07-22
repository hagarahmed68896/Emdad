<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessData extends Model
{
    protected $fillable = [
        'user_id',
        'national_id',
        'national_id_attach',
        'company_name',
        'commercial_registration',
        'national_address',
        'iban',
        'tax_certificate',
       
    ]; 

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ لا تربطه بالـ Document مباشرة!
    // إذا أردت جلب الوثائق من BusinessData:
    public function documents()
    {
        return $this->hasManyThrough(
            Document::class, // الجدول البعيد
            User::class,     // الجدول الوسيط
            'id',            // المفتاح في User الذي يرتبط بـ BusinessData.user_id
            'supplier_id',   // المفتاح في Document الذي يرتبط بـ User.id
            'user_id',       // المفتاح المحلي في BusinessData الذي يرتبط بـ User.id
            'id'             // المفتاح المحلي في User
        );
    }
}
