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
           Schema::create('ventas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
        $table->string('lugar');
        $table->text('nota')->nullable();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->decimal('subtotal', 10, 2);
        $table->decimal('descuento', 10, 2)->default(0);
        $table->decimal('envio', 10, 2)->default(0);
        $table->decimal('iva', 10, 2)->default(0);
        $table->decimal('total', 10, 2);
        $table->string('plan')->default('contado');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
