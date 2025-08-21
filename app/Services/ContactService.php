<?php

namespace App\Services;

use App\Models\ContactSubmission;
use App\Models\NewsletterSubscriber;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ContactService
{
    protected ContactRepositoryInterface $contactRepository;

    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function getAllContacts()
    {
        return $this->contactRepository->getAllContacts();
    }

    public function getNewContacts()
    {
        return $this->contactRepository->getNewContacts();
    }

    public function createContactSubmission(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                $data['status'] = 'new';
                $data['submitted_at'] = now();
                $data['ip_address'] = request()->ip();
                $data['user_agent'] = request()->userAgent();

                $contact = $this->contactRepository->create($data);

                // TODO: Send notification to admin
                // $this->sendContactNotification($contact);

                return $contact;
            } catch (\Exception $e) {
                Log::error('Failed to create contact submission: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function markAsRead(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                return $this->contactRepository->markAsRead($id);
            } catch (\Exception $e) {
                Log::error('Failed to mark contact as read: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function deleteContact(int $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                return $this->contactRepository->delete($id);
            } catch (\Exception $e) {
                Log::error('Failed to delete contact: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function subscribeToNewsletter(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                $existing = NewsletterSubscriber::where('email', $data['email'])->first();
                
                if ($existing) {
                    if (!$existing->is_active) {
                        $existing->update([
                            'is_active' => true,
                            'subscribed_at' => now()
                        ]);
                        return $existing;
                    }
                    throw new \Exception('Email already subscribed to newsletter');
                }

                $data['is_active'] = true;
                $data['subscribed_at'] = now();
                $data['ip_address'] = request()->ip();

                $subscriber = NewsletterSubscriber::create($data);

                // TODO: Send welcome email
                // $this->sendWelcomeEmail($subscriber);

                return $subscriber;
            } catch (\Exception $e) {
                Log::error('Failed to subscribe to newsletter: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function unsubscribeFromNewsletter(string $email)
    {
        return DB::transaction(function () use ($email) {
            try {
                $subscriber = NewsletterSubscriber::where('email', $email)->first();
                
                if (!$subscriber) {
                    throw new \Exception('Email not found in newsletter subscribers');
                }

                $subscriber->update([
                    'is_active' => false,
                    'unsubscribed_at' => now()
                ]);

                return $subscriber;
            } catch (\Exception $e) {
                Log::error('Failed to unsubscribe from newsletter: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function getContactsByDateRange($fromDate, $toDate)
    {
        return $this->contactRepository->getContactsByDateRange($fromDate, $toDate);
    }

    public function getContactStatistics()
    {
        try {
            return [
                'total' => ContactSubmission::count(),
                'new' => ContactSubmission::where('status', 'new')->count(),
                'read' => ContactSubmission::where('status', 'read')->count(),
                'today' => ContactSubmission::whereDate('created_at', today())->count(),
                'this_week' => ContactSubmission::where('created_at', '>=', now()->startOfWeek())->count(),
                'this_month' => ContactSubmission::where('created_at', '>=', now()->startOfMonth())->count(),
                'newsletter_subscribers' => NewsletterSubscriber::where('is_active', true)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get contact statistics: ' . $e->getMessage());
            throw $e;
        }
    }

    // TODO: Implement email notifications
    // protected function sendContactNotification($contact) { }
    // protected function sendWelcomeEmail($subscriber) { }
}