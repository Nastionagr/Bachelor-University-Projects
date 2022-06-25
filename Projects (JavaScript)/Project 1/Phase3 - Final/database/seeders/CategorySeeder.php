<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [['name'=>'Sweets','url_name'=>'sweets',], [
                ['name'=>'Bars','url_name'=>'bars', 'description'=>''],
                ['name'=>'Biscuits','url_name'=>'biscuits', 'description'=>''],
                ['name'=>'Bonbons','url_name'=>'bonbons', 'description'=>''],
                ['name'=>'Chocolate', 'url_name'=>'chocolate','description'=>'']
            ]],
            [['name'=>'Gifts','url_name'=>'gifts'],[
                ['name'=>'Birthday Gifts','url_name'=>'birthday-gifts', 'description'=>''],
                ['name'=>'Wedding Gifts','url_name'=>'wedding-gifts', 'description'=>''],
            ]],
            [['name'=>'Bulk Candy','url_name'=>'bulk-candy'], [
                ['name'=>'Boxes', 'url_name'=>'boxes','description'=>''],
                ['name'=>'Jars','url_name'=>'jars','description'=>''],
            ]],
            [['name'=>'Sugar Free & Vegan','url_name'=>'sugar-free-vegan'],[
                ['name'=>'Vegan','url_name'=>'vegan', 'description'=>''],
                ['name'=>'Sugar Free','url_name'=>'sugar-free','description'=>''],
                ['name'=>'Gluten Free','url_name'=>'gluten-free','description'=>''],
                ]],
            [['name'=>'Sale','url_name'=>'sale', 'description'=>'']]
        ];

        foreach($categories as $category) {
            $category_item = Category::create( array_values($category)[0]);
            if (count($category) > 1) {
                foreach( array_values($category)[1] as $subcategory) {
                    $sub_item = Category::create($subcategory+['parent_category_id'=>$category_item->id]);
                    $sub_item->save();
                }
            }
            $category_item->save();
        }
    }
}