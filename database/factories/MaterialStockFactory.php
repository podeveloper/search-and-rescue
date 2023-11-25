<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\StockPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialStock>
 */
class MaterialStockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'material_id' => Material::inRandomOrder()->first()?->id,
            'stock_place_id' => StockPlace::inRandomOrder()->first()?->id,
            'lower_limit' => fake()->randomElement([100,200,300]),
            'current_amount' => fake()->randomElement([3000,5000,6000,10000]),
        ];
    }
}
