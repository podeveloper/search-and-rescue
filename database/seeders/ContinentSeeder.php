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
            ['name' => 'Asya'],
            ['name' => 'Avrupa'],
            ['name' => 'Afrika'],
            ['name' => 'Kuzey Amerika'],
            ['name' => 'Güney Amerika'],
            ['name' => 'Okyanusya'],
            ['name' => 'Antarktika'],
        ];

        DB::table('continents')->insert($continents);
    }
}
