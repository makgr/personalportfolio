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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject'); // subject_type and subject_id
            $table->nullableMorphs('causer'); // causer_type and causer_id
            $table->json('properties')->nullable();
            $table->string('event')->nullable(); // created, updated, deleted
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index('log_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_log');
    }
};
