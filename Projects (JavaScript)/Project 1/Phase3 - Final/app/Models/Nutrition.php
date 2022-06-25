<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nutrition extends Model
{
    use HasFactory;

    protected $fillable = [
        'energy_kj',
        'energy_kcal',
        'fat',
        'fat_saturated',
        'carbohydrates',
        'carbohydrates_sugar',
        'protein'
    ];

    public function product()
    {
        return $this->hasOne(Product::class);
    }
}