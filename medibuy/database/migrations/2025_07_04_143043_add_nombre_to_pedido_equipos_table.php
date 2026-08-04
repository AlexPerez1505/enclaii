<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreToPedidoEquiposTable extends Migration
{
    public function up()
    {
        Schema::table('pedido_equipos', function (Blueprint $table) {
            $table->string('nombre')->after('pedido_id')->nullable(false)->default('')->comment('Nombre libre del equipo');
            
            // Opcional: si ya tienes columna equipo_id, puedes decidir quitarla después con otra migración
            // $table->dropColumn('equipo_id');
        });
    }

    public function down()
    {
        Schema::table('pedido_equipos', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });
    }
}
