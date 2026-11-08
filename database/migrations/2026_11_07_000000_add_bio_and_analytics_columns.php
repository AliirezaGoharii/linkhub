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
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('username');
            $table->unsignedBigInteger('profile_views')->default(0)->after('banner_path');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->unsignedBigInteger('click_count')->default(0)->after('ordering');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'profile_views']);
        });

        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('click_count');
        });
    }
};
