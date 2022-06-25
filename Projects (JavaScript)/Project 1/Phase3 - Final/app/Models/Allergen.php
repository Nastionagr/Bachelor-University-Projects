<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type'
    ];

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_allergens',
            'allergen_id',
            'product_id');
    }
}