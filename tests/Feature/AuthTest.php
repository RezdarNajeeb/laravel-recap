<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a new user successfully', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => 'password123',
    ];

    $response = $this->postJson('/api/register', $payload);

    $response->assertCreated()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token',
            ],
        ]);

    // Confirm user is stored in DB with hashed password
    $user = User::where('email', $payload['email'])->first();
    expect($user)->not()->toBeNull()
        ->and(Hash::check($payload['password'], $user->password))->toBeTrue();
});

it('fails login with invalid credentials', function () {
    // Make sure user exists
    $user = User::factory()->create([
        'password' => bcrypt('correct_password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong_password',
    ]);

    $response->assertUnauthorized()
        ->assertJson([
            'status' => false,
            'message' => 'Invalid credentials',
        ]);
});

it('logs in successfully with correct credentials', function () {
    $password = 'correct_password';
    $user = User::factory()->create([
        'password' => bcrypt($password),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token',
            ],
        ]);
});

it('logs out an authenticated user', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->deleteJson('/api/logout');

    $response->assertOk()
        ->assertJson([
            'status' => true,
            'message' => 'User logged out successfully',
        ]);

    // Assert tokens deleted
    expect($user->tokens()->count())->toBe(0);
});

