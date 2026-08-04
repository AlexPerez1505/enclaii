<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixForeignKeyOnOrdenesTable extends Migration
{
    public function up()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // Cambia 'ordenes_equipo_id_foreign' por el nombre real de tu FK si es diferente
            $table->dropForeign('ordenes_equipo_id_foreign');

            $table->foreign('aparato_id')->references('id')->on('aparatos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropForeign(['aparato_id']);

            $table->foreign('aparato_id')->references('id')->on('equipos')->onDelete('cascade');
        });
    }
}
