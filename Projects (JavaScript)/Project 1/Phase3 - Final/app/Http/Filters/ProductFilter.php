<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends QueryFilter
{

    public function brand($brand)
    {
        $brands = array_filter(explode(',', $brand));
        $this->builder->where(function (Builder $query) use ($brands) {
            foreach ($brands as $brand) {
                $query->orWhere('brand', 'ilike', $brand);
            }
        });
    }

    public function originCountry($country)
    {
        $countries = array_filter(explode(',', $country));
        $this->builder->where(function (Builder $query) use ($countries) {
            foreach ($countries as $country) {
                $query->orWhere('origin_country', 'ilike', $country);
            }
        });
    }

    public function priceMin($price)
    {
        $this->builder->where(function (Builder $query) use ($price) {
            $query->orWhere('price', '>', $price);
        });
    }

    public function priceMax($price)
    {
        $this->builder->where(function (Builder $query) use ($price) {
            $query->orWhere('price', '<', $price);
        });
    }

    protected function sort(string $value)
    {
        switch ($value) {
            case 'newest':
                $this->builder->orderBy('updated_at','desc');
                break;
            case 'oldest':
                $this->builder->orderBy('updated_at','asc');
                break;
            case 'a-z':
                $this->builder->orderBy('brand','asc')->orderBy('name','asc');
                break;
            case 'z-a':
                $this->builder->orderBy('brand','desc')->orderBy('name','desc');
                break;
            case 'price-low':
                $this->builder->orderBy('price','asc');
                break;
            case 'price-high':
                $this->builder->orderBy('price','desc');
                break;
        }
    }
}