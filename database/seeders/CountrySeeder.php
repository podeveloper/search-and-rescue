<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Nationality;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Load the JSON data
        $json = file_get_contents(database_path('jsons/countries_states_cities.json'));
        $data = json_decode($json, true);

        DB::transaction(function () use ($data) {
            foreach ($data as $countryData) {

                if (!empty($countryData['subregion_id']))
                {
                    $region = DB::table('regions')->updateOrInsert(
                        ['id' => $countryData['subregion_id']],
                        [
                            'name' => $countryData['subregion'],
                        ]
                    );
                }

                if (!empty($countryData['nationality']))
                {
                    $nationality = DB::table('nationalities')->updateOrInsert(
                        [
                            'id' => $countryData['id'],
                            'name' => $countryData['nationality_tr'],
                        ]
                    );
                }

                // Create the Country record
                $country = DB::table('countries')->insertGetId([
                    'id' => $countryData['id'],
                    'name' => $countryData["translations"]["tr"],
                    'name_en' => $countryData['name'],
                    'iso3' => $countryData['iso3'],
                    'iso2' => $countryData['iso2'],
                    'numeric_code' => $countryData['numeric_code'],
                    'phone_code' => $countryData['phone_code'],
                    'capital' => $countryData['capital'],
                    'currency' => $countryData['currency'],
                    'currency_name' => $countryData['currency_name'],
                    'currency_symbol' => $countryData['currency_symbol'],
                    'tld' => $countryData['tld'],
                    'native' => $countryData['native'],
                    'latitude' => $countryData['latitude'],
                    'longitude' => $countryData['longitude'],
                    'emoji' => $countryData['emoji'],
                    'emojiU' => $countryData['emojiU'],
                    'region_id' => !empty($countryData['subregion_id']) ? $countryData['subregion_id'] : null,
                    'continent_id' => !empty($countryData['continent_id']) ? $countryData['continent_id'] : null,
                    'nationality_id' => $countryData['id'],
                ]);

                // Create cities for the country
                $cities = [];
                foreach ($countryData['states'] as $cityData) {
                    $cities[] = [
                        'id' => $cityData["id"],
                        'name' => str_replace(["'","`","‘","’","ʻ"],'',$cityData['name']),
                        'code' => $cityData['state_code'],
                        'country_id' => $countryData["id"],
                    ];

                    $districts = [];
                    foreach ($cityData['cities'] as $districtData) {
                        $districts[] = [
                            'id' => $districtData["id"],
                            'name' => str_replace(["'","`","‘","’","ʻ"],'',$districtData['name']),
                            'city_id' => $cityData["id"],
                        ];
                    }

                    DB::table('districts')->insert($districts);
                }

                DB::table('cities')->insert($cities);
            }
        });
    }
}
