<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemisionFieldsToOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // ✅ Fecha real para el próximo mantenimiento (opcional)
            if (!Schema::hasColumn('ordenes', 'proximo_mantenimiento_fecha')) {
                $table->date('proximo_mantenimiento_fecha')
                    ->nullable()
                    ->after('proximo_mantenimiento');
            }

            // ✅ Campos de remisión / factura
            if (!Schema::hasColumn('ordenes', 'remision_cantidad')) {
                $table->unsignedInteger('remision_cantidad')
                    ->nullable()
                    ->after('mto_realizado');
            }

            if (!Schema::hasColumn('ordenes', 'remision_precio')) {
                $table->decimal('remision_precio', 10, 2)
                    ->nullable()
                    ->after('remision_cantidad');
            }

            if (!Schema::hasColumn('ordenes', 'remision_subtotal')) {
                $table->decimal('remision_subtotal', 10, 2)
                    ->nullable()
                    ->after('remision_precio');
            }

            if (!Schema::hasColumn('ordenes', 'remision_unidad')) {
                $table->string('remision_unidad', 50)
                    ->nullable()
                    ->after('remision_subtotal');
            }

            if (!Schema::hasColumn('ordenes', 'remision_descripcion')) {
                $table->text('remision_descripcion')
                    ->nullable()
                    ->after('remision_unidad');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            if (Schema::hasColumn('ordenes', 'remision_descripcion')) {
                $table->dropColumn('remision_descripcion');
            }
            if (Schema::hasColumn('ordenes', 'remision_unidad')) {
                $table->dropColumn('remision_unidad');
            }
            if (Schema::hasColumn('ordenes', 'remision_subtotal')) {
                $table->dropColumn('remision_subtotal');
            }
            if (Schema::hasColumn('ordenes', 'remision_precio')) {
                $table->dropColumn('remision_precio');
            }
            if (Schema::hasColumn('ordenes', 'remision_cantidad')) {
                $table->dropColumn('remision_cantidad');
            }
            if (Schema::hasColumn('ordenes', 'proximo_mantenimiento_fecha')) {
                $table->dropColumn('proximo_mantenimiento_fecha');
            }
        });
    }
}
