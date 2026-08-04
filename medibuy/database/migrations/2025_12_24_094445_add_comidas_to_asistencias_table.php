<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Si ya tienes hora_salida, no la repitas
            if (!Schema::hasColumn('asistencias', 'hora_salida')) {
                $table->time('hora_salida')->nullable()->after('hora');
            }

            // Almuerzo (11:15 - 11:30)
            if (!Schema::hasColumn('asistencias', 'hora_almuerzo_salida')) {
                $table->time('hora_almuerzo_salida')->nullable()->after('hora_salida');
            }
            if (!Schema::hasColumn('asistencias', 'hora_almuerzo_regreso')) {
                $table->time('hora_almuerzo_regreso')->nullable()->after('hora_almuerzo_salida');
            }

            // Comida (14:30 - 15:30)
            if (!Schema::hasColumn('asistencias', 'hora_comida_salida')) {
                $table->time('hora_comida_salida')->nullable()->after('hora_almuerzo_regreso');
            }
            if (!Schema::hasColumn('asistencias', 'hora_comida_regreso')) {
                $table->time('hora_comida_regreso')->nullable()->after('hora_comida_salida');
            }

            // Para rastrear importaciones
            if (!Schema::hasColumn('asistencias', 'import_batch')) {
                $table->uuid('import_batch')->nullable()->index()->after('hora_comida_regreso');
            }
            if (!Schema::hasColumn('asistencias', 'import_source')) {
                $table->string('import_source', 50)->nullable()->after('import_batch'); // 'entrada_salida' o 'comida'
            }
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            foreach ([
                'hora_almuerzo_salida',
                'hora_almuerzo_regreso',
                'hora_comida_salida',
                'hora_comida_regreso',
                'import_batch',
                'import_source',
            ] as $col) {
                if (Schema::hasColumn('asistencias', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
