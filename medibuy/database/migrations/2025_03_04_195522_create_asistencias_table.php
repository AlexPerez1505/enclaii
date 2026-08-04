<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('fecha'); // Día registrado
            $table->time('hora'); // Nueva columna para la hora
            $table->enum('estado', ['asistencia', 'falta', 'permiso', 'vacaciones', 'retardo'])->default('asistencia'); // Estados actualizados
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
};
