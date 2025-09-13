<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title','product_id','status','can_send_messages', 'block_until'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function product()
{
    return $this->belongsTo(\App\Models\Product::class);
}

  // المورد (من خلال المنتج)
    public function supplier()
    {
        return $this->hasOneThrough(
            BusinessData::class,  // Model المورد
            Product::class,       // Model المنتج
            'id',                 // المفتاح الأساسي في جدول المنتجات
            'id',                 // المفتاح الأساسي في جدول BusinessData
            'product_id',         // المفتاح في جدول conversations
            'business_data_id'    // المفتاح في جدول products
        );
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

}

