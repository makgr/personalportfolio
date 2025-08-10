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
        Schema::create('blog_post_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained()->onDelete('cascade');
            $table->integer('version_number');
            $table->json('data');
            $table->string('changed_by')->nullable();
            $table->text('change_notes')->nullable();
            $table->timestamps();
            $table->index(['blog_post_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_versions');
    }
};
