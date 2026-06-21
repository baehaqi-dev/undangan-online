<?php

namespace Database\Factories;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Guest>
 */
class GuestFactory extends Factory
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
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'unique_token' => Str::random(16),
            'invited_at' => null,
        ];
    }
}