<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('remisions', function (Blueprint $table) {
            // Envío
            $table->boolean('tiene_envio')->default(false)->after('aplicar_iva');
            $table->decimal('envio_costo', 12, 2)->default(0)->after('tiene_envio');
            $table->string('envio_direccion')->nullable()->after('envio_costo');

            // Meses / mensualidad
            $table->unsignedInteger('meses_a_pagar')->nullable()->after('envio_direccion');
            $table->decimal('mensualidad', 12, 2)->nullable()->after('meses_a_pagar');
        });
    }

    public function down(): void
    {
        Schema::table('remisions', function (Blueprint $table) {
            $table->dropColumn([
                'tiene_envio',
                'envio_costo',
                'envio_direccion',
                'meses_a_pagar',
                'mensualidad',
            ]);
        });
    }
};
