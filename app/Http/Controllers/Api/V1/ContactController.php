<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactRequest;
use App\Services\ContactService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Submit contact form
     */
    public function store(ContactRequest $request)
    {
        try {
            // Rate limiting
            $key = 'contact-form:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 3)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.',
                ], 429);
            }

            $contact = $this->contactService->createContactSubmission($request->validated());

            // Send email notification to site owner
            $this->sendContactNotification($contact);

            RateLimiter::hit($key, 3600); // 1 hour

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you soon.',
                'data' => [
                    'id' => $contact->id,
                    'submitted_at' => $contact->submitted_at
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to submit contact form: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit your message. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        try {
            // Rate limiting
            $key = 'newsletter:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.',
                ], 429);
            }

            $subscriber = $this->contactService->subscribeToNewsletter($request->only(['email']));

            RateLimiter::hit($key, 3600); // 1 hour

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to newsletter!',
                'data' => [
                    'email' => $subscriber->email,
                    'subscribed_at' => $subscriber->created_at->toISOString()
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to subscribe to newsletter: ' . $e->getMessage());
            
            $message = $e->getMessage();
            if (str_contains($message, 'already subscribed')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already subscribed to our newsletter.'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $this->contactService->unsubscribeFromNewsletter($request->email);

            return response()->json([
                'success' => true,
                'message' => 'Successfully unsubscribed from newsletter.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to unsubscribe. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Send email notification to site owner about new contact submission
     */
    private function sendContactNotification($contact)
    {
        try {
            // Get email settings
            $settings = Setting::whereIn('key', [
                'contact_email',
                'site_title',
                'mail_driver',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_encryption',
                'mail_from_address',
                'mail_from_name'
            ])->pluck('value', 'key');

            $contactEmail = $settings->get('contact_email');
            
            if (!$contactEmail) {
                Log::info('No contact email configured, skipping notification');
                return;
            }

            // Configure mail settings dynamically
            $this->configureMailSettings($settings);

            $subject = 'New Contact Form Submission - ' . ($settings->get('site_title', 'Portfolio'));
            
            $message = "
Hello,

You have received a new contact form submission on your portfolio website.

Contact Details:
================
Name: {$contact->name}
Email: {$contact->email}
Phone: " . ($contact->phone ?: 'Not provided') . "
Subject: " . ($contact->subject ?: 'General Inquiry') . "

Message:
========
{$contact->message}

Additional Information:
======================
Submitted: {$contact->created_at}
IP Address: " . request()->ip() . "
User Agent: " . request()->userAgent() . "

---
This is an automated notification from your portfolio website.
Please do not reply to this email directly. Contact the sender at: {$contact->email}
            ";

            Mail::raw($message, function ($mail) use ($contactEmail, $subject, $contact, $settings) {
                $mail->to($contactEmail)
                     ->subject($subject)
                     ->from(
                         $settings->get('mail_from_address', config('mail.from.address')),
                         $settings->get('mail_from_name', config('mail.from.name', 'Portfolio'))
                     )
                     ->replyTo($contact->email, $contact->name);
            });

            Log::info('Contact notification email sent successfully', [
                'contact_id' => $contact->id,
                'recipient' => $contactEmail
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send contact notification email: ' . $e->getMessage(), [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Configure mail settings dynamically
     */
    private function configureMailSettings($settings)
    {
        $mailDriver = $settings->get('mail_driver', 'mail');
        
        if ($mailDriver === 'smtp' && $settings->get('mail_host')) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $settings->get('mail_host'),
                'mail.mailers.smtp.port' => $settings->get('mail_port', 587),
                'mail.mailers.smtp.encryption' => $settings->get('mail_encryption', 'tls'),
                'mail.mailers.smtp.username' => $settings->get('mail_username'),
                'mail.mailers.smtp.password' => $settings->get('mail_password'),
                'mail.from.address' => $settings->get('mail_from_address'),
                'mail.from.name' => $settings->get('mail_from_name'),
            ]);
        } else {
            config([
                'mail.default' => 'mail',
                'mail.from.address' => $settings->get('mail_from_address', config('mail.from.address')),
                'mail.from.name' => $settings->get('mail_from_name', config('mail.from.name')),
            ]);
        }
    }
}
