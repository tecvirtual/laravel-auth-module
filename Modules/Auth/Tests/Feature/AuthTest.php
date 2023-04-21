<?php

namespace Modules\Auth\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test a successful user login.
     *
     * @return void
     */
    public function testUserLogin()
    {
        // Create a test user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        // Send a login request
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Check response status
        $response->assertStatus(200);

        // Check response data
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);

    }

    /**
     * Test a successful user registration.
     *
     * @return void
     */
    public function testUserRegistration()
    {
        // Generate random user data
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        // Send a registration request
        $response = $this->postJson('/api/auth/register', $userData);

        // Check response status
        $response->assertStatus(201);

        // Check response data
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    /**
     * Test a successful user retrieval.
     *
     * @return void
     */
    public function testUserRetrieval()
    {
        // Create a test user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        // Generate a token for the user
        $token = auth('api')->login($user);

        // Send a user retrieval request
        $response = $this->get('/api/auth/user', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Check response status
        $response->assertStatus(200);

        // Check response data
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
