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
       Schema::create('recepcion_componentes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('recepcion_id')->constrained('recepciones')->onDelete('cascade');
    $table->foreignId('componente_id')->constrained()->onDelete('cascade');
    $table->integer('cantidad_recibida');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepcion_componentes');
    }
};
