<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationLevels = [
            ['name' => 'Ortaokul'],
            ['name' => 'Lise'],
            ['name' => 'Lisans'],
            ['name' => 'Yüksek Lisans'],
            ['name' => 'Doktora'],
        ];

        DB::table('education_levels')->insert($educationLevels);
    }
}
