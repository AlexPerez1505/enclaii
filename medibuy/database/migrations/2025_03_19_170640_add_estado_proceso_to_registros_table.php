<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->enum('estado_proceso', ['registro', 'hojalateria', 'mantenimiento', 'stock', 'vendido','defectuoso'])
                  ->default('registro');
        });
    }

    public function down()
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->dropColumn('estado_proceso');
        });
    }
};
