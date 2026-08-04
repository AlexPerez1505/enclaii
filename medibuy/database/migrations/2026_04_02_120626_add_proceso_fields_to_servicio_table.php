<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicio', function (Blueprint $table) {
            if (!Schema::hasColumn('servicio', 'estado_proceso')) {
                $table->string('estado_proceso')->nullable()->after('nombre_doctor');
            }

            if (!Schema::hasColumn('servicio', 'mantenimiento_tipo')) {
                $table->string('mantenimiento_tipo')->default('interno')->after('estado_proceso');
            }

            if (!Schema::hasColumn('servicio', 'orden_id')) {
                $table->unsignedBigInteger('orden_id')->nullable()->after('mantenimiento_tipo');
            }

            if (!Schema::hasColumn('servicio', 'orden_validada_at')) {
                $table->timestamp('orden_validada_at')->nullable()->after('orden_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('servicio', function (Blueprint $table) {
            if (Schema::hasColumn('servicio', 'orden_validada_at')) {
                $table->dropColumn('orden_validada_at');
            }

            if (Schema::hasColumn('servicio', 'orden_id')) {
                $table->dropColumn('orden_id');
            }

            if (Schema::hasColumn('servicio', 'mantenimiento_tipo')) {
                $table->dropColumn('mantenimiento_tipo');
            }

            if (Schema::hasColumn('servicio', 'estado_proceso')) {
                $table->dropColumn('estado_proceso');
            }
        });
    }
};