<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Http\Filters\ProductFilter;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function show($category, ProductFilter $filter)
    {
        $categories = Category::getAll();
        $categories_parsed = explode('/', $category);
        $lastCategory = array_pop($categories_parsed);
        $category = Category::where('url_name',$lastCategory)->with('parent')->first();
        $products_filter = Product::whereHas('categories.parent', function($query) use($lastCategory) {$query->where('url_name', '=', $lastCategory);})
        ->orWhereHas('categories', function($query) use($lastCategory) {$query->where('url_name', '=', $lastCategory);})->where('is_offered', true);
        $brand = with(clone $products_filter)->distinct()->pluck('brand');
        $origin_country = with(clone $products_filter)->distinct()->pluck('origin_country');
        $items = [['name'=>'brand','fullname'=>'Brand','data'=>$brand], ['name'=>'origin_country','fullname'=>'Country of origin','data'=>$origin_country]];
        $filter_price_min = with(clone $products_filter)->min('price');
        $filter_price_max =  with(clone $products_filter)->max('price');
        $products = $products_filter->filter($filter);
        if (is_null($products_filter->getQuery()->orders)) {
            $products = $products->orderBy('sold','desc');
        }
        $products = $products->paginate(12);
        $filter = compact('items','filter_price_max', 'filter_price_min');
        return view('products.catalogue',compact('products','category','filter','categories'));
    }
}