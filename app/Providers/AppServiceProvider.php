<?php

namespace App\Providers;

use App\Models\ContactSubmission;
use App\Models\Setting;
use App\View\Composers\ProfileComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Show dates in the timezone picked in Settings; rescue keeps boot working before migrations run
        $timezone = rescue(fn () => Setting::get('timezone'), null, false);
        if ($timezone && in_array($timezone, \DateTimeZone::listIdentifiers(), true)) {
            config(['app.display_timezone' => $timezone]);
        }

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
