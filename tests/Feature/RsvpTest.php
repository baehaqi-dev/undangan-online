<?php

use App\Models\Guest;
use App\Models\Invitation;

test('guest can view invitation via valid token', function () {
    $invitation = Invitation::factory()->create();
    $guest = Guest::factory()->create([
        'invitation_id' => $invitation->id,
        'unique_token' => 'valid-token-123',
    ]);

    $response = $this->getJson("/api/v1/rsvp/{$guest->unique_token}");

    $response->assertStatus(200)
        ->assertJsonPath('guest_name', $guest->name);
});

test('invalid token returns 404', function () {
    $response = $this->getJson('/api/v1/rsvp/invalid-token-xyz');

    $response->assertStatus(404);
});

test('guest can submit rsvp without login', function () {
    $invitation = Invitation::factory()->create();
    $guest = Guest::factory()->create(['invitation_id' => $invitation->id]);

    $response = $this->postJson("/api/v1/rsvp/{$guest->unique_token}", [
        'guest_name' => $guest->name,
        'attendance' => 'hadir',
        'total_guests' => 2,
        'message' => 'Selamat ya!',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.attendance', 'hadir');

    $this->assertDatabaseHas('rsvps', [
        'guest_id' => $guest->id,
        'attendance' => 'hadir',
    ]);
});

test('rsvp requires valid attendance value', function () {
    $invitation = Invitation::factory()->create();
    $guest = Guest::factory()->create(['invitation_id' => $invitation->id]);

    $response = $this->postJson("/api/v1/rsvp/{$guest->unique_token}", [
        'guest_name' => $guest->name,
        'attendance' => 'mungkin-kali-ya',
        'total_guests' => 1,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('attendance');
});

test('first visit updates invited_at timestamp', function () {
    $invitation = Invitation::factory()->create();
    $guest = Guest::factory()->create([
        'invitation_id' => $invitation->id,
        'invited_at' => null,
    ]);

    $this->getJson("/api/v1/rsvp/{$guest->unique_token}");

    expect($guest->fresh()->invited_at)->not->toBeNull();
});