<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEquipoIdFromRecepcionesTable extends Migration
{
    public function up()
    {
        Schema::table('recepciones', function (Blueprint $table) {
            // ✅ Primero elimina la foreign key si existe
            if (Schema::hasColumn('recepciones', 'equipo_id')) {
                $table->dropForeign(['equipo_id']);
                $table->dropColumn('equipo_id');
            }
        });
    }

    public function down()
    {
        Schema::table('recepciones', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_id')->nullable();

            // Opcionalmente puedes restaurar la relación si era con la tabla `equipos`
            // $table->foreign('equipo_id')->references('id')->on('equipos')->nullOnDelete();
        });
    }
}
