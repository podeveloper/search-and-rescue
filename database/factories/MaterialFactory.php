<?php

namespace Database\Factories;

use App\Models\MaterialCategory;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->title,
            'material_category_id' => MaterialCategory::inRandomOrder()->first()?->id,
        ];
    }
}
