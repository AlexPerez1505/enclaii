<?php
// database/migrations/2026_02_28_000001_add_detalle_metodos_to_pagos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // JSON con desglose: [{metodo, monto}, ...]
            $table->json('detalle_metodos')->nullable()->after('metodo_pago');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn('detalle_metodos');
        });
    }
};