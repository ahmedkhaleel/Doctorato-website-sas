<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The admin login flow has three sequential guards. These tests pin
 * each so a regression that drops, say, the is_active check doesn't
 * silently re-enable off-boarded accounts.
 */
class AdminLoginGatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_with_correct_password_logs_in_directly(): void
    {
        $user = User::factory()->create([
            'email' => 'active@example.com',
            'password' => Hash::make('correct-horse'),
            'is_active' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'active@example.com',
            'password' => 'correct-horse',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_wrong_password_does_not_log_in(): void
    {
        User::factory()->create([
            'email' => 'active@example.com',
            'password' => Hash::make('correct'),
            'is_active' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'active@example.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_log_in_even_with_correct_password(): void
    {
        // Off-boarding scenario: an admin's flag is flipped to false.
        // Without this gate, they could still log in if they kept
        // their credentials.
        User::factory()->create([
            'email' => 'fired@example.com',
            'password' => Hash::make('still-knows-it'),
            'is_active' => false,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'fired@example.com',
            'password' => 'still-knows-it',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_user_with_2fa_is_redirected_to_challenge_instead_of_dashboard(): void
    {
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('letmein'),
            'is_active' => true,
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'letmein',
        ]);

        // We pass credentials but get parked at the challenge instead
        // of authenticated against the dashboard.
        $response->assertRedirect('/admin/2fa/challenge');
        $this->assertGuest();
    }

    public function test_correct_totp_code_finishes_2fa_login(): void
    {
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();

        $user = User::factory()->create([
            'password' => Hash::make('letmein'),
            'is_active' => true,
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_confirmed_at' => now(),
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'letmein',
        ]);

        $validCode = $service->generateCode($secret);
        $response = $this->post('/admin/2fa/verify', ['code' => $validCode]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_totp_code_keeps_user_on_challenge_page(): void
    {
        $service = app(TwoFactorService::class);
        $user = User::factory()->create([
            'password' => Hash::make('letmein'),
            'is_active' => true,
            'two_factor_secret' => Crypt::encryptString($service->generateSecret()),
            'two_factor_confirmed_at' => now(),
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'letmein',
        ]);

        $response = $this->post('/admin/2fa/verify', ['code' => '000000']);

        $response->assertSessionHasErrors(['code']);
        $this->assertGuest();
    }
}
