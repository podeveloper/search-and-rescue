<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EssentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(GenderSeeder::class);
        $this->call(EducationLevelSeeder::class);
        $this->call(ReferralSourceSeeder::class);
        $this->call(ContinentSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(OccupationSeeder::class);
        $this->call(RoleSeeder::class);

        $adminUser = \App\Models\User::factory()->createQuietly([
            'name' => config('seed.admin.name'),
            'surname' => 'Admin',
            'full_name' => config('seed.admin.name') . ' ' . 'Admin',
            'email' => config('seed.admin.email'),
            'username' => 'adminuser',
            'is_admin' => true,
            'password' => bcrypt(config('seed.admin.password')),
        ]);

        $adminUser->addresses()->create([
            'country_id' => '225', // Turkey
            'city_id' => '2170', // Istanbul
            'district_id' => '107933', // Kartal
            'full_address' => 'Esentepe, Füsun Sokağı No:4, 34870',
            'distance_from_center' => 0,
            'estimated_time_of_arrival' => 0,
        ]);

        $adminUser->assignRole('coordinator');
    }
}
