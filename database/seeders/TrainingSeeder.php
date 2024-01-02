<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Section;
use App\Models\Training;
use App\Models\Video;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('jsons/trainings.json'));
        $data = json_decode($json, true);

        foreach ($data['trainings'] as $trainingData) {
            $newTraining = Training::firstOrCreate(['title' => $trainingData['title']]);

            foreach ($trainingData['modules'] as $key => $moduleData) {
                $newModule = Module::firstOrCreate([
                    'title' => $moduleData['title'],
                    'training_id' => $newTraining->id,
                ]);

                foreach ($moduleData['sections'] as $sectionData) {
                    $newSection = Section::firstOrCreate([
                        'title' => $sectionData['title'],
                        'module_id' => $newModule->id,
                    ]);

                    $newVideo = Video::firstOrCreate([
                        'title' => $sectionData['title'],
                        'url' => $sectionData['videoUrl'],
                        'section_id' => $newSection->id,
                    ]);
                }
            }
        }
    }
}
