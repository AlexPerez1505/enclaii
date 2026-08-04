<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Usar el nombre real de la tabla en tu base de datos:
        $table = 'pagos_financiamiento';

        if (Schema::hasTable($table)) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (!Schema::hasColumn($table, 'monto_pendiente')) {
                    $t->decimal('monto_pendiente', 12, 2)->nullable()->after('monto');
                }
            });

            // Inicializa monto_pendiente = monto para filas que tengan null (si existe la columna)
            if (Schema::hasColumn($table, 'monto_pendiente')) {
                DB::table($table)
                    ->whereNull('monto_pendiente')
                    ->update(['monto_pendiente' => DB::raw('monto')]);
            }
        } else {
            // En desarrollo: dejar registro para debugging si quieres.
            // logger("Migración add_monto_pendiente: la tabla {$table} no existe, saltando.");
        }
    }

    public function down(): void
    {
        $table = 'pagos_financiamiento';

        if (Schema::hasTable($table)) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (Schema::hasColumn($table, 'monto_pendiente')) {
                    $t->dropColumn('monto_pendiente');
                }
            });
        }
    }
};
