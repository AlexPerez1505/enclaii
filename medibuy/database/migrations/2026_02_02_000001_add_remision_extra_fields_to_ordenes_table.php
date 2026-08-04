<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // Nuevos campos de remisión
            if (!Schema::hasColumn('ordenes', 'remision_envio')) {
                $table->decimal('remision_envio', 12, 2)->nullable()->after('remision_precio');
            }

            if (!Schema::hasColumn('ordenes', 'remision_requiere_iva')) {
                $table->boolean('remision_requiere_iva')->default(false)->after('remision_envio');
            }

            if (!Schema::hasColumn('ordenes', 'remision_iva')) {
                $table->decimal('remision_iva', 12, 2)->nullable()->after('remision_subtotal');
            }

            if (!Schema::hasColumn('ordenes', 'remision_total')) {
                $table->decimal('remision_total', 12, 2)->nullable()->after('remision_iva');
            }

            if (!Schema::hasColumn('ordenes', 'remision_anticipo')) {
                $table->decimal('remision_anticipo', 12, 2)->nullable()->after('remision_total');
            }

            if (!Schema::hasColumn('ordenes', 'remision_total_pagar')) {
                $table->decimal('remision_total_pagar', 12, 2)->nullable()->after('remision_anticipo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // Quitar en orden inverso
            if (Schema::hasColumn('ordenes', 'remision_total_pagar')) {
                $table->dropColumn('remision_total_pagar');
            }
            if (Schema::hasColumn('ordenes', 'remision_anticipo')) {
                $table->dropColumn('remision_anticipo');
            }
            if (Schema::hasColumn('ordenes', 'remision_total')) {
                $table->dropColumn('remision_total');
            }
            if (Schema::hasColumn('ordenes', 'remision_iva')) {
                $table->dropColumn('remision_iva');
            }
            if (Schema::hasColumn('ordenes', 'remision_requiere_iva')) {
                $table->dropColumn('remision_requiere_iva');
            }
            if (Schema::hasColumn('ordenes', 'remision_envio')) {
                $table->dropColumn('remision_envio');
            }
        });
    }
};
