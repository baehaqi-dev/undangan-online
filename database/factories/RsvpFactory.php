<?php

namespace Database\Factories;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Rsvp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rsvp>
 */
class RsvpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'guest_id' => Guest::factory(),
            'guest_name' => fake()->name(),
            'attendance' => fake()->randomElement(['hadir', 'tidak_hadir', 'ragu_ragu']),
            'total_guests' => fake()->numberBetween(1, 4),
            'message' => fake()->sentence(),
        ];
    }
}