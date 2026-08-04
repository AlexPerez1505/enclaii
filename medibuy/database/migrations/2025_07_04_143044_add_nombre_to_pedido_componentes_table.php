<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreToPedidoComponentesTable extends Migration
{
    public function up()
    {
        Schema::table('pedido_componentes', function (Blueprint $table) {
            $table->string('nombre')->after('pedido_id')->nullable(false)->default('')->comment('Nombre libre del componente');
            
            // Opcional: si ya tienes columna componente_id, puedes quitarla después con otra migración
            // $table->dropColumn('componente_id');
        });
    }

    public function down()
    {
        Schema::table('pedido_componentes', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });
    }
}
