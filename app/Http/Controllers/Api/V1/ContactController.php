<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactRequest;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

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
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255'
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

            $subscriber = $this->contactService->subscribeToNewsletter($request->only(['email', 'name']));

            RateLimiter::hit($key, 3600); // 1 hour

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to newsletter!',
                'data' => [
                    'email' => $subscriber->email,
                    'subscribed_at' => $subscriber->subscribed_at
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
}
