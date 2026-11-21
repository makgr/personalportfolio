<?php

// Author: Mohammad Ali Khan <xvirus.bd@gmail.com>

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Portrait shown large in the frontend's home hero: a PNG/WebP with a
     * transparent background, or a photo on a plain dark background.
     */
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('hero_cutout_url')->nullable()->after('hero_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('hero_cutout_url');
        });
    }
};
