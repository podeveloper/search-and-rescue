<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Load the JSON data
        $json = file_get_contents(database_path('jsons/languages.json'));
        $data = json_decode($json, true);

        DB::transaction(function () use ($data) {
            foreach ($data as $languageData) {

                $language = [
                    'name' => $languageData['name'],
                    'code' => strtoupper($languageData['code']),
                    'native_name' => $languageData['nativeName'],
                ];

                DB::table('languages')->insert($language);
            }
        });
    }
}
