<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = [
            ['name' => 'Erkek','name_en' => 'Male'],
            ['name' => 'Kadın','name_en' => 'Female'],
        ];

        DB::table('genders')->insert($genders);
    }
}
