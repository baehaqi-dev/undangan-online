<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slug' => fake()->unique()->slug(2),
            'groom_name' => fake()->firstName('male'),
            'bride_name' => fake()->firstName('female'),
            'event_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'akad_time' => '08:00',
            'resepsi_time' => '11:00',
            'location' => fake()->address(),
            'location_url' => fake()->url(),
            'description' => fake()->sentence(),
            'cover_image_url' => null,
        ];
    }
}