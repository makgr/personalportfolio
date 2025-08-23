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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('position');
            $table->string('department')->nullable();
            $table->text('bio')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('github')->nullable();
            $table->string('website')->nullable();
            $table->json('skills')->nullable();
            $table->date('join_date')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            $table->string('location')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->string('icon')->default('bx-user');
            $table->string('color')->default('#007bff');
            $table->timestamps();

            $table->index(['is_active', 'order']);
            $table->index(['is_featured', 'order']);
            $table->index(['department', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
