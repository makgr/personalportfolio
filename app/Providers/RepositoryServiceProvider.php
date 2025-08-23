<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Eloquent\ProjectRepository;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Eloquent\BlogRepository;
use App\Repositories\Contracts\SkillRepositoryInterface;
use App\Repositories\Eloquent\SkillRepository;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Contracts\ExperienceRepositoryInterface;
use App\Repositories\Eloquent\ExperienceRepository;
use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Eloquent\ContactRepository;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use App\Repositories\Eloquent\ProfileRepository;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\Eloquent\TeamRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
        $this->app->bind(SkillRepositoryInterface::class, SkillRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(ExperienceRepositoryInterface::class, ExperienceRepository::class);
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
