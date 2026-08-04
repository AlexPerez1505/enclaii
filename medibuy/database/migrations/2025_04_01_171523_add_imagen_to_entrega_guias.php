<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('entrega_guias', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('firmaDigital'); // Agrega la columna imagen
        });
    }

    public function down() {
        Schema::table('entrega_guias', function (Blueprint $table) {
            $table->dropColumn('imagen'); // Elimina la columna en caso de rollback
        });
    }
};
