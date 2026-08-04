<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicioTable extends Migration
{
    public function up(): void
    {
        Schema::create('servicio', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('estado_id');

            $table->string('tipo_equipo');
            $table->string('subtipo_equipo');

            $table->string('numero_serie')->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->year('año')->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_adquisicion')->nullable();

            $table->string('evidencia1')->nullable(); // Foto
            $table->string('evidencia2')->nullable(); // Foto
            $table->string('evidencia3')->nullable(); // Foto
            $table->string('video')->nullable();      // Video

            $table->text('observaciones')->nullable();
            $table->string('user_name');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicio');
    }
}
