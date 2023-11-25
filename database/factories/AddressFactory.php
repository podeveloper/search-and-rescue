<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::where('name','Turkey')->first();
        $city = $country?->cities?->first();
        $district = $city?->districts?->first();

        return [
            'country_id' => $country->id,
            'city_id' => $city?->id,
            'district_id' => $district?->id,
            'user_id' => User::inRandomOrder()?->first()->id,
        ];
    }
}
