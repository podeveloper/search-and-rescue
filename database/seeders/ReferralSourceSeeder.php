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
            ['name' => 'From A Friend'],
            ['name' => 'Social Media'],
            ['name' => 'While Visiting The Foundation'],
            ['name' => 'From A Training Program'],
            ['name' => 'Seminar At School'],
        ];

        DB::table('referral_sources')->insert($sources);
    }
}
