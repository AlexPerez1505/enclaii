<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestamo_registro', function (Blueprint $table) {
            if (!Schema::hasColumn('prestamo_registro', 'salida_scanned_at')) {
                $table->timestamp('salida_scanned_at')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('prestamo_registro', 'salida_scanned_by')) {
                $table->unsignedBigInteger('salida_scanned_by')->nullable()->after('salida_scanned_at');
            }
            if (!Schema::hasColumn('prestamo_registro', 'devolucion_scanned_at')) {
                $table->timestamp('devolucion_scanned_at')->nullable()->after('salida_scanned_by');
            }
            if (!Schema::hasColumn('prestamo_registro', 'devolucion_scanned_by')) {
                $table->unsignedBigInteger('devolucion_scanned_by')->nullable()->after('devolucion_scanned_at');
            }
            if (!Schema::hasColumn('prestamo_registro', 'vendido_scanned_at')) {
                $table->timestamp('vendido_scanned_at')->nullable()->after('devolucion_scanned_by');
            }
            if (!Schema::hasColumn('prestamo_registro', 'vendido_scanned_by')) {
                $table->unsignedBigInteger('vendido_scanned_by')->nullable()->after('vendido_scanned_at');
            }
            if (!Schema::hasColumn('prestamo_registro', 'estado_item')) {
                $table->string('estado_item', 20)->nullable()->after('vendido_scanned_by');
            }
        });

        // Backfill: marca salida para todos los registros ya existentes (si no estaba puesta)
        DB::table('prestamo_registro')
            ->whereNull('salida_scanned_at')
            ->update(['salida_scanned_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('prestamo_registro', function (Blueprint $table) {
            if (Schema::hasColumn('prestamo_registro', 'estado_item'))          $table->dropColumn('estado_item');
            if (Schema::hasColumn('prestamo_registro', 'vendido_scanned_by'))   $table->dropColumn('vendido_scanned_by');
            if (Schema::hasColumn('prestamo_registro', 'vendido_scanned_at'))   $table->dropColumn('vendido_scanned_at');
            if (Schema::hasColumn('prestamo_registro', 'devolucion_scanned_by'))$table->dropColumn('devolucion_scanned_by');
            if (Schema::hasColumn('prestamo_registro', 'devolucion_scanned_at'))$table->dropColumn('devolucion_scanned_at');
            if (Schema::hasColumn('prestamo_registro', 'salida_scanned_by'))    $table->dropColumn('salida_scanned_by');
            if (Schema::hasColumn('prestamo_registro', 'salida_scanned_at'))    $table->dropColumn('salida_scanned_at');
        });
    }
};
