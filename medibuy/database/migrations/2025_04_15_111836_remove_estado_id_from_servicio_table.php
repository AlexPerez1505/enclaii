<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEstadoIdFromServicioTable extends Migration
{
    public function up(): void
    {
        Schema::table('servicio', function (Blueprint $table) {
            $table->dropColumn('estado_id');
        });
    }

    public function down(): void
    {
        Schema::table('servicio', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_id');
        });
    }
}
