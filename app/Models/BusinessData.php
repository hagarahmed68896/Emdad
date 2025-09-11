<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessData extends Model
{
        protected $fillable = [
        'user_id',
        'national_id',
        'company_name',
        'commercial_registration',
        'national_address',
        'iban',
        'tax_certificate',
        'description',
        'experience_years',
        'start_date',
        'bank_name',
        'account_name',
        'bank_address',
        'swift_code',
       
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

//     public function products()
// {
//     return $this->hasMany(Product::class, 'business_data_id');
// }

// App\Models\BusinessData.php

public function products()
{
    return $this->hasMany(\App\Models\Product::class, 'business_data_id', 'id');
}

}
