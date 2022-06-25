<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Nutrition;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $countries = ['Slovakia', 'Czech Republic','Ukraine','Germany', 'Poland','USA','Korea'];
        $ingredients = ['Milk','Sugar','Cocoa butter','Hazelnut pieces','Cocoa mass','Milk solids','Soy lecithin','Vanilla','Cream','Butter','Salt'];
        $images = ['bar1.png','biscuit1.png','biscuit2.png','cake1.png','cupcake1.png'];
        $brands = ['Milka','Roshen','Sweetio','Delicatesso','OmNomNom','Nestle','Bonapetitoo','Haribo'];
        $name_1 = ['Great', 'Best','Delicate','White chocolate','Dark chocolate','Sweet','Jelly','Gummy','Hot', 'Winter','American', 'Ukranian', 'Nerds'];
        $name_2 = ['premium','cream','rainbow','fresh','mint','sour','nougat','toffee'];
        $name_3 = ['bar','biscuit','block','cupcake','candy','pralines','mix','bonbons','raisins','lollipop','sherbet','marshmallow'];

        return [
            'name' => implode(' ',[$this->faker->randomElement($name_1),$this->faker->randomElement($name_2),$this->faker->randomElement($name_3)]),
            'brand' => $this->faker->randomElement($brands),
            'weight' => $this->faker->numberBetween(20,300),
            'price' => $this->faker->randomFloat(2, 0.5, 20),
            'quantity' => $this->faker->numberBetween(0,100),
            'images' => $this->faker->randomElements($images, rand(1,4)),
            'ingredients' => implode(', ',$this->faker->randomElements($ingredients, rand(5,10))),
            'description' => $this->faker->text(200),
            'origin_country' => $this->faker->randomElement($countries),
            'dimensions' => implode(', ',array(rand(1,20),rand(1,20),rand(1,20))),
            'nutrition_id' => Nutrition::class::factory()->create()
        ];
    }
}