<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Http\Filters\ProductFilter;
use App\Http\Filters\SearchFilter;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function index(ProductFilter $filter, SearchFilter $search)
    {
        $categories = Category::getAll();
        $products = Product::filter($search)->where('is_offered', true);
        $brand = with(clone $products)->distinct()->pluck('brand');
        $origin_country = with(clone $products)->whereNotNull('origin_country')->distinct()->pluck('origin_country');
        $items = [['name'=>'brand','fullname'=>'Brand','data'=>$brand], ['name'=>'origin_country','fullname'=>'Country of origin','data'=>$origin_country]];
        $filter_price_min = with(clone $products)->min('price');
        $filter_price_max =  with(clone $products)->max('price');
        $products = $products->filter($filter);
        if (is_null($products->getQuery()->orders)) {
            $products = $products->orderBy('brand','asc')->orderBy('name','asc');
        }
        $products = $products->paginate(12);
        $filter = compact('items','filter_price_max', 'filter_price_min');
        return view('products.catalogue',compact('products','filter','categories'));
    }

    public function showCategory($category, ProductFilter $filter)
    {
        $categories = Category::getAll();
        $categories_parsed = explode('/', $category);
        $lastCategory = array_pop($categories_parsed);
        $category = Category::where('url_name',$lastCategory)->with('parent')->first();
        $products_filter = Product::whereHas('categories.parent', function($query) use($lastCategory) {$query->where('url_name', '=', $lastCategory);})
        ->orWhereHas('categories', function($query) use($lastCategory) {$query->where('url_name', '=', $lastCategory);})->where('is_offered', true);
        $brand = with(clone $products_filter)->distinct()->pluck('brand');
        $origin_country = with(clone $products_filter)->whereNotNull('origin_country')->distinct()->pluck('origin_country');
        $items = [['name'=>'brand','fullname'=>'Brand','data'=>$brand], ['name'=>'origin_country','fullname'=>'Country of origin','data'=>$origin_country]];
        $filter_price_min = with(clone $products_filter)->min('price');
        $filter_price_max =  with(clone $products_filter)->max('price');
        $products = $products_filter->filter($filter);
        if (is_null($products_filter->getQuery()->orders)) {
            $products = $products->orderBy('brand','asc')->orderBy('name','asc');
        }
        $products = $products->paginate(12);
        $filter = compact('items','filter_price_max', 'filter_price_min');
        return view('products.catalogue',compact('products','category','filter','categories'));
    }

    public function show(Product $product)
    {
        $categories = Category::getAll();
        return view('products.detail',compact('product','categories'));
    }

    public function home()
    {
        $categories = Category::getAll();
        $products = Product::where('is_offered', true);
        $products_new = Product::where('is_offered', true)->orderBy('updated_at','desc')->take(4)->get();
        $products_recommend = Product::where('is_offered', true)->get()->shuffle()->take(10);
        return view('home',compact('products_new', 'products_recommend','categories'));
    }
}