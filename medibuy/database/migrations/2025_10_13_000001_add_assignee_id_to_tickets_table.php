<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'assignee_id')) {
                $table->unsignedBigInteger('assignee_id')->nullable()->after('creator_id');
                $table->foreign('assignee_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'assignee_id')) {
                $table->dropForeign(['assignee_id']);
                $table->dropColumn('assignee_id');
            }
        });
    }
};
