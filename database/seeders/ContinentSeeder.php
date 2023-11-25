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
            ['name' => 'Asia'],
            ['name' => 'Europe'],
            ['name' => 'Africa'],
            ['name' => 'North America'],
            ['name' => 'South America'],
            ['name' => 'Oceania'],
            ['name' => 'Antarctica'],
        ];

        DB::table('continents')->insert($continents);
    }
}
