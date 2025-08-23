<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\CategoryController;


Route::get('/', function () {
    return view('auth.login');
});

// Authentication Routes (no prefix)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes WITHOUT prefix - just use middleware
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Projects (directly accessible at /projects)
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/publish', [ProjectController::class, 'publish'])->name('projects.publish');
    Route::post('projects/{project}/unpublish', [ProjectController::class, 'unpublish'])->name('projects.unpublish');
    Route::post('projects/bulk-action', [ProjectController::class, 'bulkAction'])->name('projects.bulk-action');

    // Blog Posts (directly accessible at /blog-posts)
    Route::resource('blog-posts', BlogController::class);
    Route::post('blog-posts/{post}/publish', [BlogController::class, 'publish'])->name('blog-posts.publish');
    Route::post('blog-posts/{post}/unpublish', [BlogController::class, 'unpublish'])->name('blog-posts.unpublish');

    // Skills (directly accessible at /skills)
    Route::resource('skills', SkillController::class);
    Route::post('skills/reorder', [SkillController::class, 'reorder'])->name('skills.reorder');
    Route::post('skills/{skill}/toggle-status', [SkillController::class, 'toggleStatus'])->name('skills.toggle-status');
    Route::post('skills/{skill}/toggle-primary', [SkillController::class, 'togglePrimary'])->name('skills.toggle-primary');

    // Services
    Route::resource('services', ServiceController::class);
    Route::post('services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
    Route::post('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

    // Experience
    Route::resource('experiences', ExperienceController::class);
    Route::post('experiences/reorder', [ExperienceController::class, 'reorder'])->name('experiences.reorder');
    Route::post('experiences/{experience}/toggle-status', [ExperienceController::class, 'toggleStatus'])->name('experiences.toggle-status');

    // Education
    Route::resource('education', EducationController::class);
    Route::post('education/reorder', [EducationController::class, 'reorder'])->name('education.reorder');
    Route::post('education/{education}/toggle-status', [EducationController::class, 'toggleStatus'])->name('education.toggle-status');
    Route::post('education/bulk-action', [EducationController::class, 'bulkAction'])->name('education.bulk-action');

    // Training
    Route::resource('admin/training', TrainingController::class, ['as' => 'admin']);
    Route::post('admin/training/reorder', [TrainingController::class, 'updateOrder'])->name('admin.training.reorder');
    Route::post('admin/training/{training}/toggle-status', [TrainingController::class, 'toggleStatus'])->name('admin.training.toggle-status');
    Route::post('admin/training/{training}/toggle-featured', [TrainingController::class, 'toggleFeatured'])->name('admin.training.toggle-featured');

    // Testimonials
    Route::resource('admin/testimonials', TestimonialController::class, ['as' => 'admin']);
    Route::post('admin/testimonials/reorder', [TestimonialController::class, 'reorder'])->name('admin.testimonials.reorder');
    Route::post('admin/testimonials/{testimonial}/toggle-featured', [TestimonialController::class, 'toggleFeatured'])->name('admin.testimonials.toggle-featured');

    // Teams
    Route::resource('teams', TeamController::class);
    Route::post('teams/reorder', [TeamController::class, 'updateOrder'])->name('teams.reorder');
    Route::post('teams/{id}/toggle-status', [TeamController::class, 'toggleStatus'])->name('teams.toggle-status');
    Route::post('teams/{id}/toggle-featured', [TeamController::class, 'toggleFeatured'])->name('teams.toggle-featured');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Contacts/Messages
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::post('contacts/{contact}/mark-as-read', [ContactController::class, 'markAsRead'])->name('contacts.mark-as-read');
    Route::post('contacts/{contact}/mark-as-replied', [ContactController::class, 'markAsReplied'])->name('contacts.mark-as-replied');
    Route::post('contacts/{contact}/archive', [ContactController::class, 'archive'])->name('contacts.archive');
    Route::post('contacts/bulk-action', [ContactController::class, 'bulkAction'])->name('contacts.bulk-action');
    Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
    Route::post('settings/test-email', [SettingController::class, 'testEmail'])->name('settings.test-email');

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});
