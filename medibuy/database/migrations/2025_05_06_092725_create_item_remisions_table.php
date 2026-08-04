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
        Schema::create('item_remisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remision_id')->constrained()->onDelete('cascade');
    
            $table->string('unidad');
            $table->integer('cantidad');
            $table->string('nombre_item');
            $table->text('descripcion_item')->nullable();
            $table->decimal('importe_unitario', 10, 2);
            $table->decimal('a_cuenta', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('restante', 10, 2);
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_remisions');
    }
};
