<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProjectController as ApiProjectController;
use App\Http\Controllers\Api\V1\BlogController as ApiBlogController;
use App\Http\Controllers\Api\V1\ContactController as ApiContactController;
use App\Http\Controllers\Api\V1\ProfileController as ApiProfileController;
use App\Http\Controllers\Api\V1\SkillController as ApiSkillController;
use App\Http\Controllers\Api\V1\ServiceController as ApiServiceController;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Response;

// API Version 1
Route::prefix('v1')->name('api.v1.')->middleware('throttle:60,1')->group(function () {

    // Public API endpoints
    Route::get('profile', [ApiProfileController::class, 'index']);
    Route::get('projects', [ApiProjectController::class, 'index']);
    Route::get('projects/featured', [ApiProjectController::class, 'featured']);
    Route::get('projects/{slug}', [ApiProjectController::class, 'show']);

    Route::get('blog-posts', [ApiBlogController::class, 'index']);
    Route::get('blog-posts/featured', [ApiBlogController::class, 'featured']);
    Route::get('blog-posts/{slug}', [ApiBlogController::class, 'show']);

    Route::get('skills', [ApiSkillController::class, 'index']);
    Route::get('services', [ApiServiceController::class, 'index']);

    Route::post('contact', [ApiContactController::class, 'store']);
    Route::post('newsletter/subscribe', [ApiContactController::class, 'subscribe']);

    // Protected API endpoints (if needed)
    Route::middleware('auth:sanctum')->group(function () {
        // Admin API endpoints
    });
});
