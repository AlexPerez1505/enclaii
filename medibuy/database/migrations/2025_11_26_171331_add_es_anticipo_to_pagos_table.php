<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Solo si no existe todavía
            if (!Schema::hasColumn('pagos', 'es_anticipo')) {
                $table->boolean('es_anticipo')
                    ->default(false)
                    ->after('metodo_pago'); // ajústalo donde te guste
            }
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'es_anticipo')) {
                $table->dropColumn('es_anticipo');
            }
        });
    }
};
