<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\ContactSubmission;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ContactApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Notification::fake();
    }

    public function test_can_submit_contact_form()
    {
        $contactData = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'phone' => $this->faker->phoneNumber
        ];

        $response = $this->postJson('/api/v1/contact', $contactData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Thank you for your message! We will get back to you soon.'
                 ]);

        $this->assertDatabaseHas('contact_submissions', [
            'name' => $contactData['name'],
            'email' => $contactData['email'],
            'subject' => $contactData['subject']
        ]);
    }

    public function test_contact_form_validation_required_fields()
    {
        $response = $this->postJson('/api/v1/contact', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_contact_form_validation_email_format()
    {
        $contactData = [
            'name' => $this->faker->name,
            'email' => 'invalid-email',
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph
        ];

        $response = $this->postJson('/api/v1/contact', $contactData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_can_subscribe_to_newsletter()
    {
        $email = $this->faker->safeEmail;

        $response = $this->postJson('/api/v1/newsletter/subscribe', [
            'email' => $email,
            'name' => $this->faker->name
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Successfully subscribed to newsletter!'
                 ]);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => $email,
            'is_subscribed' => true
        ]);
    }

    public function test_cannot_subscribe_with_same_email_twice()
    {
        $email = $this->faker->safeEmail;
        
        // First subscription
        NewsletterSubscriber::create([
            'email' => $email,
            'is_subscribed' => true
        ]);

        $response = $this->postJson('/api/v1/newsletter/subscribe', [
            'email' => $email,
            'name' => $this->faker->name
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Email is already subscribed to our newsletter.'
                 ]);
    }

    public function test_can_resubscribe_previously_unsubscribed_email()
    {
        $email = $this->faker->safeEmail;
        
        // Create unsubscribed subscriber
        NewsletterSubscriber::create([
            'email' => $email,
            'is_subscribed' => false
        ]);

        $response = $this->postJson('/api/v1/newsletter/subscribe', [
            'email' => $email,
            'name' => $this->faker->name
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Successfully subscribed to newsletter!'
                 ]);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => $email,
            'is_subscribed' => true
        ]);
    }

    public function test_can_unsubscribe_from_newsletter()
    {
        $email = $this->faker->safeEmail;
        
        // Create subscribed user
        NewsletterSubscriber::create([
            'email' => $email,
            'is_subscribed' => true
        ]);

        $response = $this->postJson('/api/v1/newsletter/unsubscribe', [
            'email' => $email
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Successfully unsubscribed from newsletter.'
                 ]);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => $email,
            'is_subscribed' => false
        ]);
    }

    public function test_unsubscribe_handles_non_existent_email()
    {
        $response = $this->postJson('/api/v1/newsletter/unsubscribe', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Email address not found in our newsletter list.'
                 ]);
    }

    public function test_newsletter_validation_requires_email()
    {
        $response = $this->postJson('/api/v1/newsletter/subscribe', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_newsletter_validation_email_format()
    {
        $response = $this->postJson('/api/v1/newsletter/subscribe', [
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}