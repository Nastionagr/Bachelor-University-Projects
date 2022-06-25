<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Filters\ProductFilter;
use App\Http\Filters\SearchFilter;
use App\Models\Product;
use App\Models\Allergen;
use App\Models\Category;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(ProductFilter $filter, SearchFilter $search)
    {
        $products = Product::filter($search);
        $categories = Category::getAll();
        $brand = with(clone $products)->distinct()->pluck('brand');
        $origin_country = with(clone $products)->whereNotNull('origin_country')->distinct()->pluck('origin_country');
        $items = [['name'=>'brand','fullname'=>'Brand','data'=>$brand], ['name'=>'origin_country','fullname'=>'Country of origin','data'=>$origin_country]];
        $filter_price_min = with(clone $products)->min('price');
        $filter_price_max =  with(clone $products)->max('price');
        $products = $products->orderBy('is_offered', 'desc');
        $products = $products->filter($filter);
        if (is_null($products->getQuery()->orders) || count($products->getQuery()->orders) == 1) {
            $products = $products->orderBy('brand','asc')->orderBy('name','asc');
        }
        $products = $products->paginate(11);
        $filter = compact('items','filter_price_max', 'filter_price_min');
        return view('admin.catalogue',compact('products','filter','categories'));
    }

    public function edit($id)
    {
        $product = Product::find($id);
        $categories = Category::getAll();
        return view('admin.detail',compact('product','categories'));
    }

    public function edit_form($id, Request $request)
    {
        $p = Product::find($id);

        $this->validate_form($request);

        $images = $this->parse_images($request);

        foreach ($p->images as $image) {
            if (!in_array($image, $images)) {
                Storage::disk('public')->delete($image);
            }
        }

        $p->name = $request->input('name');
        $p->brand = $request->input('brand');
        $p->weight = $request->input('weight');
        $p->price = $request->input('price');
        $p->quantity = $request->input('stock');
        $p->images = $images;
        $p->ingredients = $request->input('ingredients');
        $p->description = $request->input('description');
        $p->origin_country = $request->input('country');
        $p->dimensions = implode(',',$request->input('dim'));
        $p->is_offered = strtolower($request->input('offered')) == 'true' ? true : false;

        $p->categories()->detach();
        $p->categories()->save(Category::find($request->input('category')));
        if ( $request->input('is_discount') ) {
            $p->discount = $request->input('discount');
            $p->price_discounted = round($p->price - ($p->price * $request->input('discount') / 100.0), 2);
            $p->categories()->save(Category::where('name','Sale')->first());
        } else {
            $p->discount = 0;
        }

        $allergens = [];
        if ($request->input('allergens-contain')) {
            $all = array_map('trim',explode(',',$request->input('allergens-contain')));
            foreach ($all as $item) {
                array_push($allergens, Allergen::firstOrNew(['type' => 'contain', 'name' => $item]));
            }
        }
        if ($request->input('allergens-may')) {
            $all = array_map('trim',explode(',',$request->input('allergens-may')));
            foreach ($all as $item) {
                array_push($allergens, Allergen::firstOrNew(['type' => 'may contain', 'name' => $item]));
            }
        }
        $p->allergens()->detach();
        $p->allergens()->saveMany($allergens);

        if ($request->input('nutrition-kj')) {
            $p->nutrition()->energy_kj = $request->input('nutrition-kj');
            $p->nutrition()->energy_kcal = $request->input('nutrition-kcal');
            $p->nutrition()->fat = $request->input('nutrition-fat');
            $p->nutrition()->fat_saturated = $request->input('nutrition-fat-saturated');
            $p->nutrition()->carbohydrates = $request->input('nutrition-carbohydrates');
            $p->nutrition()->carbohydrates_sugar = $request->input('nutrition-carbohydrates-sugar');            
            $p->nutrition()->protein = $request->input('nutrition-protein');
        } else {
            $p->nutrition()->delete();
        }
        $p->save();

        return redirect('/admin')->withSuccess('Product has been updated.');
    }

    public function delete($id)
    {
        $product = Product::find($id);
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image);
        }   
        $product->delete();
        
        return redirect()->back();
    }

    public function create()
    {
        $categories = Category::getAll();
        return view('admin.detail', compact('categories'));
    }

    public function create_form(Request $request)
    {
        $this->validate_form($request);

        $images = $this->parse_images($request);

        $p = Product::create([
            'name' => $request->input('name'),
            'brand' => $request->input('brand'),
            'weight' => $request->input('weight'),
            'price' => $request->input('price'),
            'quantity' => $request->input('stock'),
            'images' => $images,
            'ingredients' => $request->input('ingredients'),
            'description' => $request->input('description'),
            'origin_country' => $request->input('country'),
            'dimensions' => implode(',',$request->input('dim')),
            'is_offered' => strtolower($request->input('offered')) == 'true' ? true : false
        ]);

        $p->categories()->save(Category::find($request->input('category')));
        if ( $request->input('is_discount') ) {
            $p->discount = $request->input('discount');
            $p->price_discounted = round($p->price - ($p->price * $request->input('discount') / 100.0), 2);
            $p->categories()->save(Category::where('name','Sale')->first());
            $p->save();
        }

        $allergens = [];
        if ($request->input('allergens-contain')) {
            $all = array_map('trim',explode(',',$request->input('allergens-contain')));
            foreach ($all as $item) {
                array_push($allergens, Allergen::firstOrNew(['type' => 'contain', 'name' => $item]));
            }
        }
        if ($request->input('allergens-may')) {
            $all = array_map('trim',explode(',',$request->input('allergens-may')));
            foreach ($all as $item) {
                array_push($allergens, Allergen::firstOrNew(['type' => 'may contain', 'name' => $item]));
            }
        }
        $p->allergens()->saveMany($allergens);

        if ($request->input('nutrition-kj')) {
            $p->nutrition()->create([
                'energy_kj' => $request->input('nutrition-kj'),
                'energy_kcal' => $request->input('nutrition-kcal'),
                'fat' => $request->input('nutrition-fat'),
                'fat_saturated' => $request->input('nutrition-fat-saturated'),
                'carbohydrates' => $request->input('nutrition-carbohydrates'),
                'carbohydrates_sugar' => $request->input('nutrition-carbohydrates-sugar'),
                'protein' => $request->input('nutrition-protein')
            ]);
        }

        return redirect("/admin")->withSuccess('Successfully created a new product.');
    }

    private function validate_form(Request $request) 
    {
        $request->validate([
            "images" => "required|array|min:1",
            "brand" => "required|string",
            "name" => "required|string",
            "description" => "required|string",
            "ingredients" => "string|nullable",
            "allergens-contain" => "string|nullable",
            "allergens-may" => "string|nullable",
            "country" => "string|nullable",
            "weight" => "required|integer|min:1",
            "dim" => "array|size:3",
            "dim.*" => "integer|nullable",
            "price" => "required|numeric",
            "is_discount" => "nullable",
            "discount" => "integer|nullable|required_with:is_discount,price-discounted",
            "price-discounted" => "numeric|nullable",
            "category" => "required|integer",
            "stock" => "required|integer",
            "offered" => "required",
            "nutrition-kj" => "integer|nullable|required_with:nutrition-kcal,nutrition-fat,nutrition-fat-saturated,nutrition-carbohydrates,nutrition-carbohydrates-sugar,nutrition-protein",
            "nutrition-kcal" => "integer|nullable|required_with:nutrition-kj,nutrition-fat,nutrition-fat-saturated,nutrition-carbohydrates,nutrition-carbohydrates-sugar,nutrition-protein",
            "nutrition-fat" => "numeric|nullable|required_with:nutrition-kcal,nutrition-kj,nutrition-fat-saturated,nutrition-carbohydrates,nutrition-carbohydrates-sugar,nutrition-protein",
            "nutrition-fat-saturated" => "numeric|nullable|required_with:nutrition-kcal,nutrition-fat,nutrition-kj,nutrition-carbohydrates,nutrition-carbohydrates-sugar,nutrition-protein",
            "nutrition-carbohydrates" => "numeric|nullable|required_with:nutrition-kcal,nutrition-fat,nutrition-fat-saturated,nutrition-kj,nutrition-carbohydrates-sugar,nutrition-protein",
            "nutrition-carbohydrates-sugar" => "numeric|nullable|required_with:nutrition-kcal,nutrition-fat,nutrition-fat-saturated,nutrition-carbohydrates,nutrition-kj,nutrition-protein",
            "nutrition-protein" => "numeric|nullable|required_with:nutrition-kcal,nutrition-fat,nutrition-fat-saturated,nutrition-carbohydrates,nutrition-carbohydrates-sugar,nutrition-kj"
        ]);
    }

    private function parse_images(Request $request)
    {
        $images = [];
        foreach ($request->images as $image) {
            if(count(explode(';', $image)) >= 2) {
                $name = explode(';', $image)[0];
                $img = explode(';', $image,2)[1];
                $img = preg_replace('/^data:image\/\w+;base64,/', '', $img);
                $type = explode(';', $image)[1];
                $type = explode('/', $type)[1];
                
                if (base64_encode(base64_decode($img, true)) === $img) {
                    $img_name = md5($name.time()).'.'.$type;
                    while (Storage::disk('public')->exists($img_name)) {
                        $img_name = md5($name.time()).'.'.$type;
                    }
                    Storage::disk('public')->put($img_name, base64_decode($img));
                    array_push($images,$img_name);
                } 
            }
            else {
                array_push($images,$image);
            }
        }
        return $images;
    }
}