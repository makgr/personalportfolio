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
        Schema::table('profiles', function (Blueprint $table) {
            $table->integer('years_of_experience')->default(0)->after('avatar_url');
            $table->integer('total_projects')->default(0)->after('years_of_experience');
            $table->integer('happy_clients')->default(0)->after('total_projects');
            $table->string('hero_image_url')->nullable()->after('happy_clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['years_of_experience', 'total_projects', 'happy_clients', 'hero_image_url']);
        });
    }
};
