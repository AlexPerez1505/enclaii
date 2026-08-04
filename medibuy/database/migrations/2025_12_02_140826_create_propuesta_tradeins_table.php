<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propuesta_tradeins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('propuesta_id')
                  ->constrained('propuestas')
                  ->onDelete('cascade');

            $table->string('tipo_equipo');

            // Opcionales 👇
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable();

            $table->decimal('valor_a_cuenta', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propuesta_tradeins');
    }
};
