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
        $englishJson = file_get_contents(database_path('jsons/languages.json'));
        $englishData = json_decode($englishJson, true);

        $turkishJson = file_get_contents(database_path('jsons/languages_tr.json'));
        $turkishData = json_decode($turkishJson, true);

        DB::transaction(function () use ($turkishData, $englishData) {
            foreach ($turkishData as $key => $languageData) {

                $language = [
                    'name' => $languageData['name'],
                    'name_en' => $englishData[$key]['name'],
                    'code' => strtoupper($languageData['code']),
                    'native_name' => $languageData['nativeName'],
                ];

                DB::table('languages')->insert($language);
            }
        });
    }
}
