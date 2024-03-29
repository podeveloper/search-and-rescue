<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(EssentialSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(PlacesSeeder::class);
        $this->call(VehicleSeeder::class);
        $this->call(EquipmentSeeder::class);
        // $this->call(DemoDataSeeder::class);
    }
}
