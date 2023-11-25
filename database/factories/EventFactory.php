<?php

namespace Database\Factories;

use App\Models\EventCategory;
use App\Models\EventPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title,
            'description' => fake()->paragraph,
            'date' => fake()->date('Y-m-d'),
            'starts_at' => fake()->time,
            'ends_at' => fake()->time,
            'location' => 'test location',
            'capacity' => random_int(1,100),
            'organizer' => fake()->name,
            'is_published' => true,
            'event_category_id' => EventCategory::inRandomOrder()->first()?->id,
            'event_place_id' => EventPlace::inRandomOrder()->first()?->id,
            'google_calendar_event_id' => null,
        ];
    }
}
