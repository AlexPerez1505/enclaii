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
         Schema::create('propuestas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('cliente_id');
        $table->string('lugar')->nullable();
        $table->text('nota')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->decimal('subtotal', 10, 2)->default(0);
        $table->decimal('descuento', 10, 2)->default(0);
        $table->decimal('envio', 10, 2)->default(0);
        $table->decimal('iva', 10, 2)->default(0);
        $table->decimal('total', 10, 2)->default(0);
        $table->string('plan')->nullable();
        $table->unsignedBigInteger('ficha_tecnica_id')->nullable();
        $table->unsignedBigInteger('carta_garantia_id')->nullable();
        $table->timestamps();

        $table->foreign('cliente_id')->references('id')->on('clientes');
        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('carta_garantia_id')->references('id')->on('carta_garantias');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propuestas');
    }
};
