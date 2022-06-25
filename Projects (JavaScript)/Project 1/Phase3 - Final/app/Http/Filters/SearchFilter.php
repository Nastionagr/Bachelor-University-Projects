<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class SearchFilter extends QueryFilter
{
    public function search($name)
    {
        $words = array_map('trim',explode(' ',$name));
        $this->builder->where(function (Builder $query) use ($name, $words) {
            foreach ($words as $word) {
                $query->orWhere('name', 'ilike', '%'.$word.'%');
                $query->orWhere('brand', 'ilike', '%'.$word.'%');
            }
        });
    }
}