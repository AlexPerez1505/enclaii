<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefectosToProcesosEquiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('procesos_equipos', function (Blueprint $table) {
            $table->text('defectos')->nullable(); // Añadir el campo defectos (con texto o JSON)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('procesos_equipos', function (Blueprint $table) {
            $table->dropColumn('defectos'); // Eliminar el campo defectos si es necesario revertir la migración
        });
    }
}
