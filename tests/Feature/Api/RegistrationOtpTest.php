<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_otp_code(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_requesting_otp_succeeds_for_new_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/auth/register/request-otp', [
            'email' => 'otp@example.com',
        ]);

        $response->assertStatus(200);
        $this->assertTrue(Cache::has('registration_otp_hash:otp@example.com'));
    }

    public function test_registration_creates_user_only_with_valid_otp(): void
    {
        $email = 'valid@example.com';
        Cache::put("registration_otp_hash:{$email}", Hash::make('123456'), now()->addMinutes(10));
        Cache::put("registration_otp_attempts:{$email}", 0, now()->addMinutes(10));

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Valid User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'otp_code' => '123456',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => $email]);
        $this->assertNotNull(User::where('email', $email)->first()?->email_verified_at);
    }

    public function test_registration_fails_with_invalid_otp(): void
    {
        $email = 'invalid@example.com';
        Cache::put("registration_otp_hash:{$email}", Hash::make('123456'), now()->addMinutes(10));
        Cache::put("registration_otp_attempts:{$email}", 0, now()->addMinutes(10));

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Invalid User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'otp_code' => '000000',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('users', ['email' => $email]);
    }
}
