<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCategoriaIdFromClientesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']); // Si existe una clave foránea
            $table->dropColumn('categoria_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_id')->nullable();

            // Si tenía clave foránea, la vuelves a crear aquí (opcional)
            // $table->foreign('categoria_id')->references('id')->on('categorias');
        });
    }
}
