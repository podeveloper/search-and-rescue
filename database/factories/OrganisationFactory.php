<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation>
 */
class OrganisationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $country = Country::inRandomOrder()->first();
        $city = $country?->cities?->first();

        return [
            'name' => fake()->company,
            'description' => fake()->text,
            'industry' => null,
            'phone' => fake()->phoneNumber,
            'email' => fake()->email,
            'address' => fake()->address,
            'website' => fake()->url,
            'country_id' => $country->id,
            'city_id' => $city?->id,
        ];
    }
}
