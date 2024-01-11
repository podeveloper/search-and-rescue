<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wearable Equipments
        $wearableEquipments = $this->getJsonContents('wearable_equipments.json');
        foreach ($wearableEquipments as $equipment) {
            DB::table('equipment')->insert([
                'name' => $equipment,
                'is_wearable' => true,
            ]);
        }

        // Non-wearable Equipments
        $nonWearableEquipments = $this->getJsonContents('non_wearable_equipments.json');
        foreach ($nonWearableEquipments as $equipment) {
            DB::table('equipment')->insert([
                'name' => $equipment,
                'is_wearable' => false,
            ]);
        }

        // Driving Equipments
        $drivingEquipments = $this->getJsonContents('driving_equipments.json');
        foreach ($drivingEquipments as $drivingEquipment) {
            DB::table('driving_equipment')->insert([
                'name' => $drivingEquipment,
            ]);
        }
    }

    /**
     * Get JSON contents from a file.
     *
     * @param string $filename
     * @return array
     */
    private function getJsonContents($filename)
    {
        $path = database_path("jsons/{$filename}");
        return json_decode(file_get_contents($path), true);
    }
}
