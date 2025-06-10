<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessData extends Model
{
     protected $fillable = [
        'user_id',
        'national_id',
        'national_id_attach',
        'commercial_registration',
        'commercial_registration_attach',
        'national_address',
        'national_address_attach',
        'iban',
        'iban_attach',
        'tax_certificate',
        'tax_certificate_attach',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
