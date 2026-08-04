<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // Fotos extra
            $table->string('foto_equipo_2')->nullable()->after('foto_equipo');
            $table->string('foto_equipo_3')->nullable()->after('foto_equipo_2');

            // Desglose por partidas para remisión
            $table->json('remision_partidas')->nullable()->after('remision_descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn([
                'foto_equipo_2',
                'foto_equipo_3',
                'remision_partidas',
            ]);
        });
    }
};