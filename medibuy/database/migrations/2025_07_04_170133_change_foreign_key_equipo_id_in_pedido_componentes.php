<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForeignKeyEquipoIdInPedidoComponentes extends Migration
{
    public function up()
    {
        Schema::table('pedido_componentes', function (Blueprint $table) {
            // Primero eliminamos la FK existente
            $table->dropForeign(['equipo_id']);

            // Opcional: si 'equipo_id' es NOT NULL, hacerlo nullable para evitar errores
            $table->unsignedBigInteger('equipo_id')->nullable()->change();

            // Ahora agregamos la nueva FK apuntando a pedido_equipos.id
            $table->foreign('equipo_id')
                ->references('id')
                ->on('pedido_equipos')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('pedido_componentes', function (Blueprint $table) {
            // Revertir: eliminar la FK actual
            $table->dropForeign(['equipo_id']);

            // Cambiar a nullable o no según tu esquema original
            $table->unsignedBigInteger('equipo_id')->nullable()->change();

            // Restaurar la FK original apuntando a equipos.id
            $table->foreign('equipo_id')
                ->references('id')
                ->on('equipos')
                ->onDelete('set null');
        });
    }
}
