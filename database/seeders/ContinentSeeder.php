<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContinentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $continents = [
            ['name' => 'Asya','name_en' => 'Asia'],
            ['name' => 'Avrupa','name_en' => 'Europe'],
            ['name' => 'Afrika','name_en' => 'Africa'],
            ['name' => 'Kuzey Amerika','name_en' => 'North America'],
            ['name' => 'Güney Amerika','name_en' => 'South America'],
            ['name' => 'Okyanusya','name_en' => 'Oceania'],
            ['name' => 'Antarktika','name_en' => 'Antarctica'],
        ];

        DB::table('continents')->insert($continents);
    }
}
