<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupations = json_decode(file_get_contents(database_path('jsons/occupations.json')));

        $data = [];
        foreach ($occupations as $occupation) {
            $data[] = ['name' => ucwords($occupation)];
        }

        DB::table('occupations')->insert($data);
    }
}
