<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinanciamientoIdAndAprobadoToPagosTable extends Migration
{
    public function up()
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Agrega la columna y la llave foránea a pagos_financiamiento
            $table->unsignedBigInteger('financiamiento_id')->nullable()->after('venta_id');
            $table->foreign('financiamiento_id')->references('id')->on('pagos_financiamiento')->onDelete('set null');

            // Agrega el campo 'aprobado' (opcional)
            $table->boolean('aprobado')->default(0)->after('metodo_pago');
        });
    }

    public function down()
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['financiamiento_id']);
            $table->dropColumn('financiamiento_id');
            $table->dropColumn('aprobado');
        });
    }
}
