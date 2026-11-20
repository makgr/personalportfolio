<?php

namespace App\Providers;

use App\Models\ContactSubmission;
use App\Models\Setting;
use App\View\Composers\ProfileComposer;
use Illuminate\Support\Facades\Blade;
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

        // @versioned('assets/css/x.css'): asset URL with the file's mtime, so browsers refetch only after a change
        Blade::directive('versioned', fn (string $path) => "<?php echo e(asset({$path}) . '?v=' . @filemtime(public_path({$path}))); ?>");

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
