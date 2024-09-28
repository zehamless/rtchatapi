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

