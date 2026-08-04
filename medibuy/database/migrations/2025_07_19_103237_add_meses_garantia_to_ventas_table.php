<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMesesGarantiaToVentasTable extends Migration
{
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Columna entera sin signo, nullable, justo después de carta_garantia_id
            $table->unsignedInteger('meses_garantia')
                  ->nullable()
                  ->after('carta_garantia_id');
        });
    }

    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('meses_garantia');
        });
    }
}
