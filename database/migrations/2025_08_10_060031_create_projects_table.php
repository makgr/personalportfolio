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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->longText('description');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('featured_image')->nullable();
            $table->json('images')->nullable();
            $table->enum('type', ['web', 'mobile', 'desktop', 'design', 'other'])->default('web');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('client_name')->nullable();
            $table->date('project_date')->nullable();
            $table->string('project_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->json('tech_stack')->nullable();
            $table->json('features')->nullable();
            $table->json('challenges')->nullable();
            $table->json('solutions')->nullable();
            $table->integer('duration_weeks')->nullable();
            $table->string('team_size')->nullable();
            $table->string('my_role')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('order')->default(0);
            $table->integer('views')->default(0);
            $table->json('meta_tags')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index(['status', 'is_featured']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
