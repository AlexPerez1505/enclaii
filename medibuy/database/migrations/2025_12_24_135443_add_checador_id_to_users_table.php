<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ID del checador/reloj (ej: 9). Unique para que no se repita.
            $table->unsignedBigInteger('checador_id')->nullable()->unique()->after('nomina');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['checador_id']);
            $table->dropColumn('checador_id');
        });
    }
};
