<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'discount'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}