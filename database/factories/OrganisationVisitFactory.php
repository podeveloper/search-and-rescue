<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\User;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganisationVisit>
 */
class OrganisationVisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->date('Y-m-d'),
            'place_id' => Place::inRandomOrder()->first()->id,
            'organisation_id' => Organisation::inRandomOrder()->first()->id,
            'host_id' => User::inRandomOrder()->first()->id,
            'explanation' => 'explanation',
        ];
    }
}
