<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('procesos_equipos', function (Blueprint $table) {
            // Agrega la columna checklist si no existe
            if (!Schema::hasColumn('procesos_equipos', 'checklist')) {
                // JSON (usa TEXT si tu motor no soporta JSON)
                $table->json('checklist')->nullable()->after('defectos');
                // Si tu MySQL/MariaDB es antiguo, usa:
                // $table->text('checklist')->nullable()->after('defectos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('procesos_equipos', function (Blueprint $table) {
            if (Schema::hasColumn('procesos_equipos', 'checklist')) {
                $table->dropColumn('checklist');
            }
        });
    }
};
