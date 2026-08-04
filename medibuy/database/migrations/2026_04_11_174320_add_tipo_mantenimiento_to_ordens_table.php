<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ordens')) {
            Schema::table('ordens', function (Blueprint $table) {
                if (!Schema::hasColumn('ordens', 'tipo_mantenimiento')) {
                    $table->string('tipo_mantenimiento', 30)
                        ->default('preventivo')
                        ->after('fecha_mantenimiento');
                }
            });
        }

        if (Schema::hasTable('ordenes')) {
            Schema::table('ordenes', function (Blueprint $table) {
                if (!Schema::hasColumn('ordenes', 'tipo_mantenimiento')) {
                    $table->string('tipo_mantenimiento', 30)
                        ->default('preventivo')
                        ->after('fecha_mantenimiento');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ordens') && Schema::hasColumn('ordens', 'tipo_mantenimiento')) {
            Schema::table('ordens', function (Blueprint $table) {
                $table->dropColumn('tipo_mantenimiento');
            });
        }

        if (Schema::hasTable('ordenes') && Schema::hasColumn('ordenes', 'tipo_mantenimiento')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->dropColumn('tipo_mantenimiento');
            });
        }
    }
};