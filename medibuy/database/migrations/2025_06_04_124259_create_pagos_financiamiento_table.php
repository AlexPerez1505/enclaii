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
Schema::create('pagos_financiamiento', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('venta_id');
    $table->string('descripcion'); // Ej: "Pago 1", "Pago inicial"
    $table->date('fecha_pago'); // Ej: 2025-06-04
    $table->decimal('monto', 10, 2); // Ej: 76400.00
    $table->timestamps();

    $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_financiamiento');
    }
};
