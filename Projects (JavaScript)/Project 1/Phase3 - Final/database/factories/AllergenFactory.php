<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Allergen;

class AllergenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $get_allergen = function (string $allergen, string $type): Allergen
        {
            return Allergen::firstOrNew(
                ['type' => $type, 'name' => $allergen]
            );
        };

        $allergens = ['Cereals', 'Eggs', 'Peanuts', 'Soybeans', 'Milk', 'Almonds', 'Hazelnuts', 'Pistachio', 'Cinnamon', 'Chocolate', 'Cocoa', 'Coconut'];

        $all1 = array_map($get_allergen, $allergens, array_fill(0,count($allergens),'contain'));

        $all2 = array_map($get_allergen, $allergens, array_fill(0,count($allergens),'may contain'));

        return array_merge($this->faker->randomElements($all1, rand(1,5)), $this->faker->randomElements($all2, rand(0,5)));
    }
}