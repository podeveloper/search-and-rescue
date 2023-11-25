<?php

namespace Database\Factories;

use App\Models\Gender;
use App\Models\Nationality;
use App\Models\Occupation;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = ['Ahmet','Mehmet','Ali','Ahmet'];
        $surnames = ['Özkan', 'Korkmaz', 'Karaca', 'Özkurt', 'Bozkurt'];

        return [
            'full_name' => fake()->randomElement($names) . ' ' . fake()->randomElement($surnames),
            'phone' => fake()->phoneNumber,
            'email' => fake()->email,
            'address' => fake()->address,
            'gender_id' => Gender::inRandomOrder()->first()->id,
            'nationality_id' => Nationality::inRandomOrder()->first()->id,
            'organisation_id' => Organisation::inRandomOrder()->first()->id,
            'occupation_id' => Occupation::inRandomOrder()->first()->id,
            'explanation' => "explanation",
        ];
    }
}
