<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\ContactSubmission;
use App\View\Composers\ProfileComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register profile composer for all views using the layout
        View::composer(['layouts.app', 'admin.profile.edit'], ProfileComposer::class);

        // Top-bar notifications: unread contact messages
        View::composer('layouts.app', function ($view) {
            $view->with([
                'newMessages' => ContactSubmission::new()->latest()->take(5)->get(),
                'newMessagesCount' => ContactSubmission::new()->count(),
            ]);
        });
    }
}
