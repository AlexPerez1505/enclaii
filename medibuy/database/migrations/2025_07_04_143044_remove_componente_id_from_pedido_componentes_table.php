<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveComponenteIdFromPedidoComponentesTable extends Migration
{
    public function up()
    {
        Schema::table('pedido_componentes', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_componentes', 'componente_id')) {
                $table->dropForeign(['componente_id']); // si existe foreign key
                $table->dropColumn('componente_id');
            }
        });
    }

    public function down()
    {
        Schema::table('pedido_componentes', function (Blueprint $table) {
            $table->foreignId('componente_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
}
