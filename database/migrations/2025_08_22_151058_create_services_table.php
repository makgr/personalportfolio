<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('icon')->nullable(); // For service icon (Boxicons, Font Awesome)
            $table->string('color', 7)->default('#6c757d'); // Hex color for theming
            $table->decimal('price', 10, 2)->nullable(); // Service price
            $table->enum('price_type', ['fixed', 'hourly', 'project', 'custom'])->default('fixed');
            $table->string('duration')->nullable(); // e.g., "1-2 weeks", "Ongoing"
            $table->json('features')->nullable(); // List of service features
            $table->json('technologies')->nullable(); // Technologies used
            $table->string('featured_image')->nullable(); // Service image
            $table->json('gallery')->nullable(); // Additional images
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('show_on_homepage')->default(false);
            $table->integer('order')->default(0);
            $table->string('contact_button_text')->default('Get Quote');
            $table->string('external_url')->nullable(); // Link to external service page
            $table->json('meta_tags')->nullable(); // SEO meta tags
            $table->timestamps();
            
            // Indexes
            $table->index(['is_active', 'order']);
            $table->index(['is_featured', 'order']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
