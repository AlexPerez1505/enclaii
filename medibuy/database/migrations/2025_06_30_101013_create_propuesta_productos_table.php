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
         Schema::create('propuesta_productos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('propuesta_id');
        $table->unsignedBigInteger('producto_id');
        $table->integer('cantidad')->default(1);
        $table->decimal('precio_unitario', 10, 2)->default(0);
        $table->decimal('subtotal', 10, 2)->default(0);
        $table->decimal('sobreprecio', 10, 2)->default(0);
        $table->timestamps();

        $table->foreign('propuesta_id')->references('id')->on('propuestas')->onDelete('cascade');
        $table->foreign('producto_id')->references('id')->on('productos');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propuesta_productos');
    }
};
