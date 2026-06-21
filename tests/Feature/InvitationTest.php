<?php

use App\Models\Invitation;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test-token')->plainTextToken;
});

test('authenticated user can create invitation', function () {
    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
        ->postJson('/api/v1/invitations', [
            'slug' => 'test-wedding',
            'groom_name' => 'Budi',
            'bride_name' => 'Ani',
            'event_date' => now()->addMonth()->format('Y-m-d'),
            'location' => 'Gedung Serbaguna',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.slug', 'test-wedding');

    $this->assertDatabaseHas('invitations', [
        'slug' => 'test-wedding',
        'user_id' => $this->user->id,
    ]);
});

test('guest cannot create invitation', function () {
    $response = $this->postJson('/api/v1/invitations', [
        'slug' => 'test-wedding',
        'groom_name' => 'Budi',
        'bride_name' => 'Ani',
        'event_date' => now()->addMonth()->format('Y-m-d'),
        'location' => 'Gedung Serbaguna',
    ]);

    $response->assertStatus(401);
});

test('invitation creation requires valid data', function () {
    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
        ->postJson('/api/v1/invitations', [
            'slug' => '',
            'groom_name' => '',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug', 'groom_name', 'bride_name', 'event_date', 'location']);
});

test('user can only see their own invitations', function () {
    $otherUser = User::factory()->create();

    Invitation::factory()->count(2)->create(['user_id' => $this->user->id]);
    Invitation::factory()->count(3)->create(['user_id' => $otherUser->id]);

    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
        ->getJson('/api/v1/invitations');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('user cannot update invitation they do not own', function () {
    $otherUser = User::factory()->create();
    $invitation = Invitation::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
        ->putJson("/api/v1/invitations/{$invitation->id}", [
            'location' => 'Lokasi Baru',
        ]);

    $response->assertStatus(403);
});

test('user can update their own invitation', function () {
    $invitation = Invitation::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
        ->putJson("/api/v1/invitations/{$invitation->id}", [
            'location' => 'Lokasi Baru',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.location', 'Lokasi Baru');
});

test('user cannot delete invitation they do not own', function () {
    $otherUser = User::factory()->create();
    $invitation = Invitation::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
        ->deleteJson("/api/v1/invitations/{$invitation->id}");

    $response->assertStatus(403);

    $this->assertDatabaseHas('invitations', ['id' => $invitation->id]);
});