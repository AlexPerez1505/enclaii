<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // Si tienes la columna aparato_id, la hacemos nullable para dejar de usarla.
            if (Schema::hasColumn('ordenes', 'aparato_id')) {
                $table->unsignedBigInteger('aparato_id')->nullable()->change();
            }

            // Nuevos campos manuales
            if (!Schema::hasColumn('ordenes', 'equipo')) {
                $table->string('equipo', 180)->nullable()->after('fecha_mantenimiento');
            }
            if (!Schema::hasColumn('ordenes', 'marca')) {
                $table->string('marca', 120)->nullable()->after('equipo');
            }
            if (!Schema::hasColumn('ordenes', 'modelo')) {
                $table->string('modelo', 120)->nullable()->after('marca');
            }
            if (!Schema::hasColumn('ordenes', 'numero_serie')) {
                $table->string('numero_serie', 140)->nullable()->after('modelo');
            }
            if (!Schema::hasColumn('ordenes', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('numero_serie');
            }
            if (!Schema::hasColumn('ordenes', 'foto_equipo')) {
                $table->string('foto_equipo', 255)->nullable()->after('observaciones');
            }
            if (!Schema::hasColumn('ordenes', 'proximo_mantenimiento')) {
                $table->unsignedTinyInteger('proximo_mantenimiento')->default(6)->after('foto_equipo');
            }

            if (!Schema::hasColumn('ordenes', 'mto_preventivo')) {
                $table->json('mto_preventivo')->nullable()->after('proximo_mantenimiento');
            }
            if (!Schema::hasColumn('ordenes', 'mto_realizado')) {
                $table->json('mto_realizado')->nullable()->after('mto_preventivo');
            }

            if (!Schema::hasColumn('ordenes', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('mto_realizado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // No revertimos aparato_id a NOT NULL para no romper datos al bajar migración.
            if (Schema::hasColumn('ordenes', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            if (Schema::hasColumn('ordenes', 'mto_realizado')) {
                $table->dropColumn('mto_realizado');
            }
            if (Schema::hasColumn('ordenes', 'mto_preventivo')) {
                $table->dropColumn('mto_preventivo');
            }
            if (Schema::hasColumn('ordenes', 'proximo_mantenimiento')) {
                $table->dropColumn('proximo_mantenimiento');
            }
            if (Schema::hasColumn('ordenes', 'foto_equipo')) {
                $table->dropColumn('foto_equipo');
            }
            if (Schema::hasColumn('ordenes', 'observaciones')) {
                $table->dropColumn('observaciones');
            }
            if (Schema::hasColumn('ordenes', 'numero_serie')) {
                $table->dropColumn('numero_serie');
            }
            if (Schema::hasColumn('ordenes', 'modelo')) {
                $table->dropColumn('modelo');
            }
            if (Schema::hasColumn('ordenes', 'marca')) {
                $table->dropColumn('marca');
            }
            if (Schema::hasColumn('ordenes', 'equipo')) {
                $table->dropColumn('equipo');
            }
        });
    }
};
