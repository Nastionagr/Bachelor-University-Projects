<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NutritionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'energy_kj' => $this->faker->numberBetween(1000, 3000),
            'energy_kcal' => $this->faker->numberBetween(100, 500),
            'fat' => $this->faker->randomFloat(2,0,30),
            'fat_saturated' => $this->faker->randomFloat(2,0,30),
            'carbohydrates' => $this->faker->randomFloat(2,0,10),
            'carbohydrates_sugar' => $this->faker->randomFloat(2,0,15),
            'protein' => $this->faker->randomFloat(2,0,25)
        ];
    }
}