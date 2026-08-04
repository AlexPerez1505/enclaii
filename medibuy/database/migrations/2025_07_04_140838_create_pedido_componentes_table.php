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
       Schema::create('pedido_componentes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pedido_id')->constrained()->onDelete('cascade');
    $table->foreignId('equipo_id')->nullable()->constrained()->onDelete('set null'); // opcional
    $table->foreignId('componente_id')->constrained()->onDelete('cascade');
    $table->integer('cantidad_esperada');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_componentes');
    }
};
