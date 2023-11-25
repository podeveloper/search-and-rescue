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

        \App\Models\User::factory()->createQuietly([
            'name' => config('seed.admin.name'),
            'surname' => 'Admin',
            'full_name' => config('seed.admin.name') . ' ' . 'Admin',
            'email' => config('seed.admin.email'),
            'is_admin' => true,
            'password' => bcrypt(config('seed.admin.password')),
        ]);
    }
}
