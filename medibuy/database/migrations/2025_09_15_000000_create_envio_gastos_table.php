<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('envio_gastos', function (Blueprint $t) {
            $t->id();
            $t->string('referencia')->nullable();       // Ej: "ERBE"
            $t->string('sucursal');                     // Ej: "Reynosa"
            $t->string('destino')->nullable();          // Ciudad/Estado opcional
            $t->string('transportista')->nullable();    // Ej: "Estafeta", "DHL", etc.

            // Dimensiones opcionales
            $t->decimal('alto_cm', 8, 2)->nullable();
            $t->decimal('largo_cm', 8, 2)->nullable();
            $t->decimal('ancho_cm', 8, 2)->nullable();

            $t->decimal('peso_kg', 8, 2)->nullable();
            $t->decimal('peso_volumetrico_kg', 8, 2)->nullable(); // calculado si hay dimensiones
            $t->decimal('peso_facturable_kg', 8, 2)->nullable();  // max(real, volumétrico)

            $t->decimal('costo_mxn', 12, 2);            // Lo que nos costó
            $t->date('fecha_envio');                    // Fecha real del envío

            $t->text('notas')->nullable();

            $t->timestamps();
            $t->index(['fecha_envio', 'sucursal']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('envio_gastos');
    }
};
