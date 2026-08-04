<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEquipoIdFromPedidoEquiposTable extends Migration
{
    public function up()
    {
        Schema::table('pedido_equipos', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_equipos', 'equipo_id')) {
                $table->dropForeign(['equipo_id']); // si existe foreign key
                $table->dropColumn('equipo_id');
            }
        });
    }

    public function down()
    {
        Schema::table('pedido_equipos', function (Blueprint $table) {
            $table->foreignId('equipo_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
}
