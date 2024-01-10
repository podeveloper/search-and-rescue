<?php

namespace Database\Seeders;

use App\Models\VehicleBrand;
use App\Models\VehicleCategory;
use App\Models\VehicleModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->insertJsonData('vehicle_brands');
        $this->insertJsonData('vehicle_categories');
        $this->insertJsonData('vehicle_models');
    }

    private function insertJsonData(string $tableName): void
    {
        $json = file_get_contents(database_path("jsons/{$tableName}.json"));
        $data = json_decode($json, true);
        $data = $data["$tableName"];

        $timestamp = now();
        $data = array_map(function ($item) use ($timestamp) {
            return array_merge($item, [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }, $data);

        DB::table($tableName)->insert($data);
    }
}
