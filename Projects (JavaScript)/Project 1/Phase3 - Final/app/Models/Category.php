<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_category_id'
    ];

    public function parent() 
    {
        return $this->belongsTo(Category::class, 'parent_category_id')->with('parent');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_category_id')->with('children');
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_categories',
            'category_id',
            'product_id');
    }

    public static function getAll()
    {
        return Category::whereNull('parent_category_id')->with('children')->get();
    }
}