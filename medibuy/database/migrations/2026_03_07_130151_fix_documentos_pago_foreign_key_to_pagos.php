<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Si existe la tabla, corregimos la FK
        if (Schema::hasTable('documentos_pago')) {
            Schema::table('documentos_pago', function (Blueprint $table) {
                // intentamos soltar la foreign key vieja
                try {
                    $table->dropForeign('documentos_pago_pago_id_foreign');
                } catch (\Throwable $e) {
                    // por si ya no existe o tiene otro nombre
                }
            });

            Schema::table('documentos_pago', function (Blueprint $table) {
                $table->foreign('pago_id')
                    ->references('id')
                    ->on('pagos')
                    ->onDelete('cascade');
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('documentos_pago')) {
            Schema::table('documentos_pago', function (Blueprint $table) {
                try {
                    $table->dropForeign('documentos_pago_pago_id_foreign');
                } catch (\Throwable $e) {
                    //
                }
            });

            Schema::table('documentos_pago', function (Blueprint $table) {
                $table->foreign('pago_id')
                    ->references('id')
                    ->on('pagos_financiamiento')
                    ->onDelete('cascade');
            });
        }

        Schema::enableForeignKeyConstraints();
    }
};