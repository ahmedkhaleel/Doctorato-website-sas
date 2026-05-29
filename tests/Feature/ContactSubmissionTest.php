<?php

namespace Tests\Feature;

use App\Mail\ContactAdminNotification;
use App\Mail\ContactCustomerConfirmation;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Coverage for the /contact form submission pipeline.
 *
 * The flow is brittle precisely because it intersects three things:
 *   - Bot defenses (honeypot, timing, reCAPTCHA)
 *   - User-facing validation (phone, email, max lengths)
 *   - Side-effects (DB insert + 2 emails)
 *
 * Each test isolates one slice so a regression is easy to pinpoint.
 */
class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /** A baseline of valid form input — reused across tests, mutated per case. */
    protected array $validPayload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '+1 (555) 123-4567',
        'country_code' => '+20',
        'subject' => 'Sales inquiry',
        'message' => 'I want to know more about your dental module.',
        'hp_trap' => '',
        'form_rendered_at' => 0,
        'recaptcha_token' => '',
    ];

    public function test_valid_submission_creates_a_contact_message(): void
    {
        Mail::fake();

        $response = $this->from('/contact')->post('/contact', $this->validPayload);

        $response->assertRedirect('/contact');
        $response->assertSessionHas('success', true);
        $response->assertSessionMissing('errors');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'subject' => 'Sales inquiry',
            // Phone is country-code-prefixed by the controller.
            'phone' => '+20 +1 (555) 123-4567',
        ]);
    }

    public function test_valid_submission_dispatches_both_emails(): void
    {
        Mail::fake();

        $this->post('/contact', $this->validPayload);

        // Customer confirmation lands in the submitter's inbox.
        Mail::assertQueued(ContactCustomerConfirmation::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });

        // Admin notification lands at the central inbox, not back at
        // the submitter — easy mistake to make in a refactor.
        Mail::assertQueued(ContactAdminNotification::class, function ($mail) {
            return $mail->hasTo('info@doctorato.com');
        });
    }

    public function test_long_phone_numbers_are_accepted(): void
    {
        Mail::fake();

        // Real users format phones with brackets and dashes; the
        // controller used to silently reject anything > 20 chars.
        // Regression guard so we don't reintroduce that ceiling.
        $payload = array_merge($this->validPayload, [
            'phone' => '+971 (50) 123-4567 ext 9999',
        ]);

        $response = $this->post('/contact', $payload);

        $response->assertSessionHas('success', true);
        $this->assertDatabaseCount('contact_messages', 1);
    }

    public function test_missing_required_fields_are_rejected(): void
    {
        Mail::fake();

        $response = $this->from('/contact')->post('/contact', [
            'name' => '',
            'email' => 'not-an-email',
            'subject' => '',
            'message' => '',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingQueued();
    }

    public function test_honeypot_hit_rejects_the_submission(): void
    {
        Mail::fake();

        $payload = array_merge($this->validPayload, [
            'hp_trap' => 'http://spam-bot.example/',
        ]);

        $response = $this->from('/contact')->post('/contact', $payload);

        // The controller returns the generic "verification" error so
        // we don't leak which defense fired.
        $response->assertSessionHasErrors(['message']);
        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingQueued();
    }

    public function test_email_failure_does_not_block_the_save(): void
    {
        // Even if SMTP is down, the lead must still land in the DB so
        // admins see it in the dashboard. The controller wraps each
        // Mail::send in its own try/catch for this reason.
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP unreachable'));

        $response = $this->post('/contact', $this->validPayload);

        $response->assertSessionHas('success', true);
        $this->assertDatabaseHas('contact_messages', ['email' => 'test@example.com']);
    }
}
