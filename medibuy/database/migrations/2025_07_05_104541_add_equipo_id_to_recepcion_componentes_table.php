<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recepcion_componentes', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_id')->after('recepcion_id')->nullable();

            // Si deseas la relación foránea, descomenta esta línea:
            // $table->foreign('equipo_id')->references('id')->on('equipos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recepcion_componentes', function (Blueprint $table) {
            // Si agregaste la foreign key, elimina primero:
            // $table->dropForeign(['equipo_id']);
            $table->dropColumn('equipo_id');
        });
    }
};
