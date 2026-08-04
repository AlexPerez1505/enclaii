<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFichaTecnicaIdFromPropuestasTable extends Migration
{
    public function up()
    {
        Schema::table('propuestas', function (Blueprint $table) {
            $table->dropColumn('ficha_tecnica_id');
        });
    }

    public function down()
    {
        Schema::table('propuestas', function (Blueprint $table) {
            // Aquí agrega la columna nuevamente en caso de rollback (ajusta el tipo según lo que tenías)
            $table->unsignedBigInteger('ficha_tecnica_id')->nullable();
        });
    }
}
