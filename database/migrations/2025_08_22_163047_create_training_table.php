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
        Schema::create('training', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('provider'); // Institution/Platform name
            $table->string('type')->default('course'); // course, certification, workshop, bootcamp, degree
            $table->string('category')->default('technical'); // technical, soft_skills, business, design, other
            $table->text('description')->nullable();
            $table->string('certificate_url')->nullable();
            $table->string('credential_id')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration_hours')->nullable(); // Total duration in hours
            $table->string('status')->default('completed'); // completed, in_progress, planned
            $table->integer('score')->nullable(); // Score or grade (0-100)
            $table->json('skills_gained')->nullable(); // Array of skills learned
            $table->string('instructor')->nullable();
            $table->decimal('rating', 2, 1)->nullable(); // Personal rating 1-5
            $table->text('notes')->nullable(); // Personal notes about the training
            $table->string('icon')->default('bx-book'); // Boxicons class
            $table->string('color')->default('#007bff'); // Hex color code
            $table->integer('order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'is_active']);
            $table->index(['category', 'type']);
            $table->index(['start_date', 'end_date']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training');
    }
};
