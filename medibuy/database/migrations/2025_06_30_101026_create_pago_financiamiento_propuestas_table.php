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
        Schema::create('pago_financiamiento_propuestas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('propuesta_id');
        $table->string('descripcion')->nullable();
        $table->date('fecha_pago');
        $table->decimal('monto', 10, 2)->default(0);
        $table->timestamps();

        $table->foreign('propuesta_id')->references('id')->on('propuestas')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_financiamiento_propuestas');
    }
};
