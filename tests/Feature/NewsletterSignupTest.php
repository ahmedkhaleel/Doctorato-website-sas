<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterSignupTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_email_creates_subscriber(): void
    {
        $response = $this->from('/')->post('/newsletter', [
            'email' => 'reader@example.com',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'reader@example.com',
            'is_active' => true,
        ]);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $response = $this->from('/')->post('/newsletter', [
            'email' => 'not-an-email',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('newsletter_subscribers', 0);
    }

    public function test_duplicate_email_does_not_create_a_second_row(): void
    {
        NewsletterSubscriber::factory()->create([
            'email' => 'reader@example.com',
            'is_active' => true,
        ]);

        $this->post('/newsletter', ['email' => 'reader@example.com']);

        // Confirms we don't end up with two rows for the same address.
        // If the controller is doing firstOrCreate or unique-constraint
        // handling, the count stays 1.
        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }
}
