<?php

// database/migrations/xxxx_xx_xx_create_solicitudes_materiales_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudesMaterialesTable extends Migration
{
    public function up()
    {
        Schema::create('solicitudes_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quien solicita
            $table->string('categoria'); // Papelería, Limpieza, etc.
            $table->string('material'); // Bolígrafo, Jabón, etc.
            $table->integer('cantidad');
            $table->text('justificacion')->nullable();

            $table->string('estado')->default('Pendiente'); // Pendiente, En Planta, Entregado
            $table->timestamp('fecha_entrega')->nullable(); // Cuando se entregó
            $table->foreignId('entregado_por')->nullable()->constrained('users')->nullOnDelete(); // Admin que entregó

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('solicitudes_materiales');
    }
}
