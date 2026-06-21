<?php

use App\Models\User;

test('user can register', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Baehaqi',
        'email' => 'baehaqi@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['message', 'user', 'token']);

    $this->assertDatabaseHas('users', [
        'email' => 'baehaqi@test.com',
    ]);
});

test('user cannot register with duplicate email', function () {
    User::factory()->create(['email' => 'baehaqi@test.com']);

    $response = $this->postJson('/api/v1/register', [
        'name' => 'Baehaqi',
        'email' => 'baehaqi@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

test('user can login with correct credentials', function () {
    User::factory()->create([
        'email' => 'baehaqi@test.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'baehaqi@test.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'user', 'token']);
});

test('user cannot login with wrong password', function () {
    User::factory()->create([
        'email' => 'baehaqi@test.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'baehaqi@test.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
});

test('authenticated user can access protected route', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/v1/user');

    $response->assertStatus(200)
        ->assertJson(['id' => $user->id]);
});

test('guest cannot access protected route', function () {
    $response = $this->getJson('/api/v1/user');

    $response->assertStatus(401);
});