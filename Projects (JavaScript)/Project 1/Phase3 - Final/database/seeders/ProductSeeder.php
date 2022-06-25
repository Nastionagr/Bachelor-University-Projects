<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Allergen;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::class::factory(200)->create()->each(function($p) {
            $allergens = Allergen::class::factory(1)->definition();
            $p->allergens()->saveMany($allergens);
            $categories = Category::whereNotNull('parent_category_id')->inRandomOrder()->first();
            if (rand(1,100) > 92) {
                $p->discount = rand(1,15)*5;
                $p->price_discounted = round($p->price - ( $p->price * $p->discount / 100),2);
                $p->categories()->saveMany([$categories,Category::where('name','Sale')->first()]);
                $p->save();
            }
            else {
                $p->categories()->save($categories);
            }
        });
    }
}