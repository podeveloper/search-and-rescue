<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferralSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            ['name' => 'Arkadaş'],
            ['name' => 'Sosyal Medya'],
            ['name' => 'Saha Eğitimi'],
            ['name' => 'Seminer'],
            ['name' => 'Etkinlik'],
        ];

        DB::table('referral_sources')->insert($sources);
    }
}
