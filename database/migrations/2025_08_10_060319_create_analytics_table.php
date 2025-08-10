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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('visitor_id')->nullable();
            $table->string('page_type'); // 'home', 'project', 'blog', 'service', etc
            $table->string('page_id')->nullable(); // ID of specific content
            $table->string('page_url');
            $table->string('referrer')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('os')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->integer('time_on_page')->nullable(); // seconds
            $table->json('events')->nullable(); // Click events, scroll depth, etc
            $table->timestamp('visited_at');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index(['page_type', 'visited_at']);
            $table->index('session_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('analytics');
    }
};
