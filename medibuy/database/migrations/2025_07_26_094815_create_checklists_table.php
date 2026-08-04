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
       Schema::create('checklists', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('item_id');
        $table->enum('etapa', ['ingenieria', 'embalaje', 'entrega']);
        $table->unsignedBigInteger('user_id'); // Quien inicia el checklist
        $table->timestamp('fecha_inicio')->nullable();
        $table->timestamp('fecha_fin')->nullable();
        $table->string('tipo_entrega')->nullable(); // paqueteria/hospital
        $table->timestamps();

        $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
