<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('recepcion_componentes', function (Blueprint $table) {
            $table->dropForeign(['componente_id']); // si tenía relación
            $table->dropColumn('componente_id');
        });
    }

    public function down()
    {
        Schema::table('recepcion_componentes', function (Blueprint $table) {
            $table->unsignedBigInteger('componente_id')->nullable();
            // $table->foreign('componente_id')->references('id')->on('componentes'); // si existía
        });
    }
};
