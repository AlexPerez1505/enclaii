<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_venta_producto_componentes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venta_id');
            $table->unsignedBigInteger('producto_id'); // Producto vendido
            $table->unsignedBigInteger('componente_id'); // El componente (ej. eliminador, cable, etc)
            $table->string('estado')->nullable(); // bueno, funcional, defectuoso, NO_VINO
            $table->string('observaciones')->nullable(); // Detalles extras o justificación
            $table->timestamps();

            $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_venta_producto_componentes');
    }
};
