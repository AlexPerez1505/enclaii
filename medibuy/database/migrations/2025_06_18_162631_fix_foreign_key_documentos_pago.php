<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('documentos_pago', function (Blueprint $table) {
            $table->dropForeign(['pago_id']);
            $table->foreign('pago_id')
                ->references('id')
                ->on('pagos_financiamiento')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('documentos_pago', function (Blueprint $table) {
            $table->dropForeign(['pago_id']);
            $table->foreign('pago_id')
                ->references('id')
                ->on('pagos')
                ->onDelete('cascade');
        });
    }
};
