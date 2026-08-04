<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('remisions', function (Blueprint $table) {
            $table->dropColumn([
                'cantidad',
                'nombre_item',
                'descripcion_item',
                'importe_unitario',
                'a_cuenta',
                'restante'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('remisions', function (Blueprint $table) {
            $table->integer('cantidad')->nullable();
            $table->string('nombre_item')->nullable();
            $table->text('descripcion_item')->nullable();
            $table->decimal('importe_unitario', 10, 2)->nullable();
            $table->decimal('a_cuenta', 10, 2)->nullable();
            $table->decimal('restante', 10, 2)->nullable();
        });
    }
};
