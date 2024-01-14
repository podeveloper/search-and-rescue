<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Event;
use App\Models\Organisation;
use App\Models\OrganisationVisit;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Place;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Objects
        $dateNow = now()->toDateString();

        User::factory(10)->create();
        Organisation::factory(10)->create();
        Contact::factory(10)->create();
        Address::factory(10)->create();
        Material::factory(10)->create();
        MaterialStock::factory(10)->create();

        $country = Country::where('name', 'Turkey')->first();
        $city = $country?->cities?->first();
        $district = $city?->districts?->first();

        // Social Accounts
        User::inRandomOrder()->first()->socialAccounts()->create([
            'user_id' => User::inRandomOrder()->first()->id,
            'platform' => 'twitter',
            'username' => 'admin',
        ]);

        // Tags
        User::inRandomOrder()->first()->tags()->create(['name' => 'Test Tag']);

        // Categories
        $eventCategories = [['name' => 'Test Category']];
        DB::table('event_categories')->insert($eventCategories);

        $materialCategories = [
            ['name' => 'Metal'],
            ['name' => 'Wooden'],
        ];
        DB::table('material_categories')->insert($materialCategories);

        $contactCategories = [
            ['name' => 'Domestic Corporate Contacts'],
            ['name' => 'International Corporate Contacts'],
            ['name' => 'Followers'],
            ['name' => 'Others'],
        ];
        DB::table('contact_categories')->insert($contactCategories);

        // Places
        $places = [['name' => 'Test Place', 'type' => 'center']];
        DB::table('event_places')->insert($places);
        DB::table('places')->insert($places);
        DB::table('stock_places')->insert($places);

        // Events
        Event::factory(10)->create();

        // Organisation Visits
        OrganisationVisit::factory(10)->create();
    }
}
