<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProjectController as ApiProjectController;
use App\Http\Controllers\Api\V1\BlogController as ApiBlogController;
use App\Http\Controllers\Api\V1\ContactController as ApiContactController;
use App\Http\Controllers\Api\V1\ProfileController as ApiProfileController;
use App\Http\Controllers\Api\V1\SkillController as ApiSkillController;
use App\Http\Controllers\Api\V1\ServiceController as ApiServiceController;
use App\Http\Controllers\Api\V1\ExperienceController as ApiExperienceController;
use App\Http\Controllers\Api\V1\EducationController as ApiEducationController;
use App\Http\Controllers\Api\V1\TrainingController as ApiTrainingController;
use App\Http\Controllers\Api\V1\TestimonialController as ApiTestimonialController;
use App\Http\Controllers\Api\V1\TeamController as ApiTeamController;
use App\Http\Controllers\Api\V1\SettingsController as ApiSettingsController;
use App\Http\Controllers\Api\V1\CategoryController as ApiCategoryController;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Response;

// API Version 1
Route::prefix('v1')->name('api.v1.')->middleware('throttle:60,1')->group(function () {

    // Public API endpoints
    
    // Profile
    Route::get('profile', [ApiProfileController::class, 'index']);
    
    // Projects
    Route::get('projects', [ApiProjectController::class, 'index']);
    Route::get('projects/featured', [ApiProjectController::class, 'featured']);
    Route::get('projects/{slug}', [ApiProjectController::class, 'show']);

    // Blog Posts
    Route::get('blog-posts', [ApiBlogController::class, 'index']);
    Route::get('blog-posts/featured', [ApiBlogController::class, 'featured']);
    Route::get('blog-posts/{slug}', [ApiBlogController::class, 'show']);

    // Skills
    Route::get('skills', [ApiSkillController::class, 'index']);
    
    // Services
    Route::get('services', [ApiServiceController::class, 'index']);
    
    // Experience
    Route::get('experiences', [ApiExperienceController::class, 'index']);
    
    // Education
    Route::get('education', [ApiEducationController::class, 'index']);
    
    // Training
    Route::get('training', [ApiTrainingController::class, 'index']);
    Route::get('training/featured', [ApiTrainingController::class, 'featured']);
    
    // Testimonials
    Route::get('testimonials', [ApiTestimonialController::class, 'index']);
    Route::get('testimonials/featured', [ApiTestimonialController::class, 'featured']);
    
    // Team
    Route::get('teams', [ApiTeamController::class, 'index']);
    Route::get('teams/featured', [ApiTeamController::class, 'featured']);
    Route::get('teams/{slug}', [ApiTeamController::class, 'show']);
    
    // Categories
    Route::get('categories', [ApiCategoryController::class, 'index']);
    Route::get('categories/type/{type}', [ApiCategoryController::class, 'byType']);
    Route::get('categories/{slug}', [ApiCategoryController::class, 'show']);
    
    // Settings (Public)
    Route::get('settings', [ApiSettingsController::class, 'index']);
    Route::get('settings/{key}', [ApiSettingsController::class, 'show']);

    // Contact (POST)
    Route::post('contact', [ApiContactController::class, 'store']);
    Route::post('newsletter/subscribe', [ApiContactController::class, 'subscribe']);
    Route::post('newsletter/unsubscribe', [ApiContactController::class, 'unsubscribe']);

    // Protected API endpoints (if needed)
    Route::middleware('auth:sanctum')->group(function () {
        // Admin API endpoints
    });
});
