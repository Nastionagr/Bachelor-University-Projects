<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_price',
        'payment_price',
        'total',
        'paid',
        'shipped',
        'delivered',
        'shipping_method',
        'payment_method',
        'gift_id',
        'firstName',
        'lastName',
        'email',
        'phone_number',
        'street',
        'apartment',
        'city',
        'zip_code',
        'country'
    ];

    public function cart_products()
    {
        return $this->hasMany(Cart::class);
    }

    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }
}