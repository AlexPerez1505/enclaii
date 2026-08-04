<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::table('recepcion_componentes', function (Blueprint $table) {
    $table->string('nombre_componente')->nullable();
    $table->string('nombre_equipo')->nullable();
    $table->text('observaciones')->nullable();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recepcion_componentes', function (Blueprint $table) {
            //
        });
    }
};
