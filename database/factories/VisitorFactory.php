<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Gender;
use App\Models\Language;
use App\Models\Nationality;
use App\Models\Occupation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName,
            'surname' => fake()->lastName,
            'full_name' => null,
            'gender_id' => Gender::inRandomOrder()->first()->id,
            'nationality_id' => Nationality::inRandomOrder()->first()->id,
            'country_id' => Country::inRandomOrder()->first()->id,
            'language_id' => Language::inRandomOrder()->first()->id,
            'companion_id' => User::inRandomOrder()->first()->id,
            'phone' => fake()->phoneNumber,
            'email' => fake()->email,
            'facebook' => null,
            'twitter' => null,
            'instagram' => null,
            'telegram' => null,
            'occupation_id' => Occupation::inRandomOrder()->first()->id,
            'occupation_text' => null,
            'organisation_id' => null,
            'organisation_text' => null,
            'explanation' => null,
            'profile_photo' => null,
            'group_number' => random_int(1,20),
        ];
    }
}
