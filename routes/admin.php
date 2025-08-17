<?php

// ============================================
// 1. routes/admin.php (Create this file)
// ============================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\CertificationController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\AnalyticsController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/publish', [ProjectController::class, 'publish'])->name('projects.publish');
    Route::post('projects/{project}/unpublish', [ProjectController::class, 'unpublish'])->name('projects.unpublish');
    Route::post('projects/bulk-action', [ProjectController::class, 'bulkAction'])->name('projects.bulk-action');

    // Blog Posts
    Route::resource('blog-posts', BlogController::class);
    Route::post('blog-posts/{post}/publish', [BlogController::class, 'publish'])->name('blog-posts.publish');
    Route::post('blog-posts/{post}/unpublish', [BlogController::class, 'unpublish'])->name('blog-posts.unpublish');

    // Skills
    Route::resource('skills', SkillController::class);
    Route::post('skills/reorder', [SkillController::class, 'reorder'])->name('skills.reorder');

    // Services
    Route::resource('services', ServiceController::class);
    Route::post('services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');

    // Experience
    Route::resource('experiences', ExperienceController::class);
    Route::post('experiences/reorder', [ExperienceController::class, 'reorder'])->name('experiences.reorder');

    // Education
    Route::resource('education', EducationController::class);

    // Certifications
    Route::resource('certifications', CertificationController::class);

    // Testimonials
    Route::resource('testimonials', TestimonialController::class);
    Route::post('testimonials/{testimonial}/toggle-featured', [TestimonialController::class, 'toggleFeatured'])
        ->name('testimonials.toggle-featured');

    // Contact Submissions
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::post('contacts/{contact}/mark-as-read', [ContactController::class, 'markAsRead'])->name('contacts.mark-as-read');
    Route::post('contacts/{contact}/mark-as-replied', [ContactController::class, 'markAsReplied'])->name('contacts.mark-as-replied');
    Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('settings/seo', [SettingController::class, 'seo'])->name('settings.seo');
    Route::post('settings/seo', [SettingController::class, 'updateSeo'])->name('settings.seo.update');

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Media Manager
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
});
