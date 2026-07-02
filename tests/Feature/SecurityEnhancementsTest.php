<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Credential;
use App\Services\TwofishService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_redirects_to_otp_verification(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register/verify');
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'is_verified' => false,
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user->otp_code);
    }

    public function test_otp_verification_activates_user(): void
    {
        $salt = base64_encode(random_bytes(24));
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'master_key_salt' => $salt,
            'is_verified' => false,
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->withSession([
            'temp_register_user_id' => $user->id,
            'temp_register_twofish_key' => bin2hex(random_bytes(32)),
        ])->post('/register/verify', [
            'otp_code' => '123456',
        ]);

        $response->assertRedirect('/google2fa/setup');
        $this->assertTrue($user->fresh()->is_verified);
        $this->assertAuthenticatedAs($user);
    }

    public function test_vault_inactivity_auto_lock(): void
    {
        $salt = base64_encode(random_bytes(24));
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'master_key_salt' => $salt,
            'is_verified' => true,
        ]);

        // Simulate 6 minutes ago activity (360 seconds)
        $response = $this->actingAs($user)->withSession([
            'twofish_key' => bin2hex(random_bytes(32)),
            'twofish_last_activity' => time() - 360,
        ])->get('/dashboard');

        $response->assertRedirect('/unlock');
        $this->assertFalse(session()->has('twofish_key'));
    }

    public function test_secure_metadata_encryption_in_database(): void
    {
        $salt = base64_encode(random_bytes(24));
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'master_key_salt' => $salt,
            'is_verified' => true,
        ]);

        $key = random_bytes(32);

        $response = $this->actingAs($user)->withSession([
            'twofish_key' => bin2hex($key),
            'twofish_last_activity' => time(),
        ])->post('/credentials', [
            'platform_name' => 'Facebook',
            'platform_url' => 'https://facebook.com',
            'username' => 'johnfb',
            'password' => 'mysecret123',
        ]);

        $response->assertRedirect('/credentials');

        // Check database does not contain plaintext Facebook name
        $this->assertDatabaseMissing('credentials', [
            'platform_name_encrypted' => 'Facebook',
        ]);

        $credential = Credential::first();
        $this->assertNotNull($credential->platform_name_encrypted);
        $this->assertNotNull($credential->platform_name_iv);
        $this->assertNotNull($credential->username_encrypted);
    }
}
