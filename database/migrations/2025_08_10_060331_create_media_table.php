<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('url')->nullable();
            $table->bigInteger('size'); // in bytes
            $table->json('metadata')->nullable(); // dimensions, duration, etc
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->nullableMorphs('mediable'); // This creates mediable_id, mediable_type AND their index automatically
            $table->string('collection')->nullable(); // 'gallery', 'documents', etc
            $table->integer('order')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
};
