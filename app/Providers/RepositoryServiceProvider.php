<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Eloquent\ProjectRepository;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Eloquent\BlogRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        // $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);

        // You can add more bindings here as you create more repositories
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
