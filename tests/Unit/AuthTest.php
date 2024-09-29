<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class, WithFaker::class);

it('registers a user with valid data', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(201);
    $response->assertJson(['message' => 'User created successfully']);
    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
});
it('fails to register with missing name', function () {
    $response = $this->postJson('/api/register', [
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(422);
});
it('fails to register with invalid email', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'password' => 'password',
    ]);

    $response->assertStatus(422);
});
it('fails to register with duplicate email', function () {
    User::create([
        'name' => 'Existing User',
        'email' => 'john@example.com',
        'password' => Hash::make('password')
    ]);

    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(422);
});
it('logs in a user with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password')
    ]);
    $response = $this->postJson('/api/login', [
        'email' => 'john@example.com',
        'password' => 'password'
    ]);
    $response->assertStatus(200);
    $response->assertJsonStructure(['message', 'token']);
});
it('fails to log in with incorrect password', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password')
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'john@example.com',
        'password' => 'wrongpassword'
    ]);

    $response->assertStatus(422);
});
it('fails to log in with non-existent email', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password'
    ]);

    $response->assertStatus(422);

});
it('fails to log in with missing email', function () {
    $response = $this->postJson('/api/login', [
        'password' => 'password'
    ]);

    $response->assertStatus(422);

});
it('fails to log in with missing password', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'john@example.com'
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});
it('logs out a logged-in user', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password')
    ]);
    $login = $this->postJson('/api/login', [
        'email' => 'john@example.com',
        'password' => 'password'
    ]);
    $token = $login->json('token');

    $response = $this->postJson('/api/logout', [], [
        'Authorization' => 'Bearer ' . $token
    ]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'User logged out successfully']);
    $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
});
it('fails to log out when not authenticated', function () {
    $response = $this->postJson('/api/logout');

    $response->assertStatus(401);
});
it('generates access token when password matches', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]);
    $this->actingAs($user);

    $response = $this->postJson('/api/get-reset-token', ['password' => 'password']);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Access token generated successfully']);
});

it('returns error when password does not match', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]);
    $this->actingAs($user);

    $response = $this->postJson('/api/get-reset-token', ['password' => 'wrongpassword']);

    $response->assertStatus(401)
             ->assertJson(['message' => 'Password does not match']);
});
it('resets password successfully', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
//    $tokenResponse = $this->postJson('/api/get-reset-token', ['password' => 'password']);
//    $token = $tokenResponse->json('token');
    $response = $this->postJson('/api/reset-password', [
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword'
    ]);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Password reset successfully']);
});

it('returns error when password confirmation does not match', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/reset-password', [
        'password' => 'newpassword',
        'password_confirmation' => 'differentpassword'
    ]);

    $response->assertStatus(422);
});

it('returns error when password is not provided', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/reset-password', [
        'password' => '',
        'password_confirmation' => ''
    ]);

    $response->assertStatus(422);
});
