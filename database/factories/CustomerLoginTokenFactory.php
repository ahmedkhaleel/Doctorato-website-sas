<?php

namespace Database\Factories;

use App\Models\CustomerLoginToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerLoginTokenFactory extends Factory
{
    protected $model = CustomerLoginToken::class;

    public function definition(): array
    {
        // We materialise the plain token via a static so the test
        // can read it back with $this->plainToken if it needs to
        // hit /portal/auth/{token}. The hash column stays the
        // authoritative DB value.
        $plain = bin2hex(random_bytes(32));

        return [
            'email' => 'test+' . uniqid() . '@example.invalid',
            'token_hash' => hash('sha256', $plain),
            'expires_at' => now()->addMinutes(15),
            'ip_address' => '127.0.0.1',
            // Stash so the test can grab the plain value:
            //   $token = CustomerLoginToken::factory()->create();
            //   $plain = $token->plain_token;
            'plain_token' => $plain,
        ];
    }

    public function configure(): static
    {
        // plain_token is NOT a DB column — strip before persisting.
        return $this->afterMaking(function (CustomerLoginToken $token) {
            unset($token->plain_token);
        })->afterCreating(function (CustomerLoginToken $token) {
            unset($token->plain_token);
        });
    }
}
