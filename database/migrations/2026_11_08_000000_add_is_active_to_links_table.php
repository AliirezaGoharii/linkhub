<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the is_active toggle to links so users can hide a link
     * from their public page without deleting it (and its stats).
     */
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
