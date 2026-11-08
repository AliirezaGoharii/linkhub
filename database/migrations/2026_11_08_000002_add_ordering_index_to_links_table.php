<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A composite (user_id, ordering) index serves both the user's
     * ordered link listing and every user-scoped reorder update, so
     * the single-column user_id index becomes redundant.
     */
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->index(['user_id', 'ordering']);
            $table->dropIndex('links_user_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->index('user_id');
            $table->dropIndex('links_user_id_ordering_index');
        });
    }
};
