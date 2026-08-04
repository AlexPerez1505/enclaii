<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('modulo_usos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre'); // Nombre visible del módulo
            $table->string('ruta');   // Ruta del módulo (ej: /inventario)
            $table->string('icono');  // Ícono FontAwesome
            $table->integer('usos')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('modulo_usos');
    }
};
