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
        Schema::create('remisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('unidad');
            $table->integer('cantidad');
            $table->string('nombre_item');
            $table->text('descripcion_item')->nullable();
            $table->decimal('importe_unitario', 10, 2);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('a_cuenta', 10, 2)->default(0);
            $table->decimal('restante', 10, 2)->default(0);
            $table->string('importe_letra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remisions');
    }
};
