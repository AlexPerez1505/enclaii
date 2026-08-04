<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('procesos_equipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')->constrained('registros')->onDelete('cascade'); // Relación con "registros"
            $table->enum('tipo_proceso', ['hojalateria', 'mantenimiento', 'stock','vendido','defectuoso']);
            $table->text('descripcion_proceso');
            $table->string('evidencia1')->nullable();
            $table->string('evidencia2')->nullable();
            $table->string('evidencia3')->nullable();
            $table->string('video')->nullable();
            $table->string('documento_pdf')->nullable(); // Nueva columna para almacenar el PDF
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('procesos_equipos');
    }
};

