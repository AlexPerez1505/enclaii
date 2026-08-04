<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('entrega_guias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guia_id')->constrained()->onDelete('cascade');
            $table->string('entregado_por');
            $table->date('fecha_entrega');
            $table->string('contenido');
            $table->string('numero_serie');
            $table->text('observaciones')->nullable();
            $table->string('destinatario');
            $table->text('firmaDigital');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('entrega_guias');
    }
};
