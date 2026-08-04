<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Tipo de ticket
            $table->string('ticket_type', 30)->default('incidencia')->after('visibility');
            // Área / categoría
            $table->string('area', 50)->nullable()->after('ticket_type');

            // Subtickets
            $table->unsignedBigInteger('parent_id')->nullable()->after('area');
            $table->foreign('parent_id')->references('id')->on('tickets')->nullOnDelete();

            // Checklist (JSON)
            $table->json('checklist')->nullable()->after('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['ticket_type', 'area', 'parent_id', 'checklist']);
        });
    }
};