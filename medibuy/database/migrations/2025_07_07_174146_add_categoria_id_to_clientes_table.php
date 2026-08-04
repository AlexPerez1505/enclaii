<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_id')->nullable()->after('comentarios');

            // Si tienes una tabla 'categorias' y quieres relacionarla:
            // $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Si agregaste la foreign key, elimínala primero
            // $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
        });
    }
};
