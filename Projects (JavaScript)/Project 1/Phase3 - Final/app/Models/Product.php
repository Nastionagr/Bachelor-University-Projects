<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Filters\QueryFilter;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'brand',
        'weight',
        'price',
        'quantity',
        'sold',
        'images',
        'ingredients',
        'description',
        'origin_country',
        'dimensions',
        'nutrition_id',
        'discount',
        'is_offered'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',
        'ingredients' => 'array'
    ];

    public function nutrition()
    {
        return $this->belongsTo(Nutrition::class);
    }

    public function allergens()
    {
        return $this->belongsToMany(
            Allergen::class,
            'product_allergens',
            'product_id',
            'allergen_id');
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'product_categories',
            'product_id',
            'category_id');
    }

    public function cart_products()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * @param Builder $builder
     * @param QueryFilter $filter
     */
    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        $filter->apply($builder);
    }
}