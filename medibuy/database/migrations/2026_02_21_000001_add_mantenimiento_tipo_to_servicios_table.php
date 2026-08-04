<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ✅ Si NO existe la tabla real, la creamos (fallback seguro)
        if (!Schema::hasTable('servicio')) {
            Schema::create('servicio', function (Blueprint $table) {
                $table->id();

                // Campos base (según tu Model/Controller)
                $table->unsignedBigInteger('estado_id')->nullable();

                $table->string('tipo_equipo');
                $table->string('subtipo_equipo');

                $table->string('numero_serie')->nullable();
                $table->string('marca')->nullable();
                $table->string('modelo')->nullable();

                // OJO: tu sistema usa 'año' con ñ (lo dejamos igual)
                $table->integer('año')->nullable();

                $table->longText('descripcion')->nullable();
                $table->date('fecha_adquisicion');

                $table->string('evidencia1')->nullable();
                $table->string('evidencia2')->nullable();
                $table->string('evidencia3')->nullable();
                $table->string('video')->nullable();

                $table->string('firma_digital')->nullable();
                $table->longText('observaciones')->nullable();

                $table->string('user_name');
                $table->string('nombre_doctor')->nullable();

                $table->string('estado_proceso', 30)->nullable();

                // ✅ NUEVO CONTROL INTERNO/EXTERNO + OS
                $table->string('mantenimiento_tipo', 20)->default('externo'); // externo|interno
                $table->unsignedBigInteger('orden_id')->nullable();
                $table->timestamp('orden_validada_at')->nullable();

                $table->timestamps();
            });
        }

        // ✅ Si ya existe, solo agregamos las columnas faltantes
        Schema::table('servicio', function (Blueprint $table) {

            if (!Schema::hasColumn('servicio', 'mantenimiento_tipo')) {
                $table->string('mantenimiento_tipo', 20)->default('externo');
            }

            if (!Schema::hasColumn('servicio', 'orden_id')) {
                $table->unsignedBigInteger('orden_id')->nullable();
            }

            if (!Schema::hasColumn('servicio', 'orden_validada_at')) {
                $table->timestamp('orden_validada_at')->nullable();
            }

            // si no existe estado_proceso, lo agregamos también (por control)
            if (!Schema::hasColumn('servicio', 'estado_proceso')) {
                $table->string('estado_proceso', 30)->nullable();
            }
        });

        // ✅ FK a ordenes solo si existe tabla ordenes
        if (Schema::hasTable('ordenes') && Schema::hasColumn('servicio', 'orden_id')) {
            Schema::table('servicio', function (Blueprint $table) {
                try {
                    $table->foreign('orden_id')
                        ->references('id')
                        ->on('ordenes')
                        ->nullOnDelete();
                } catch (\Throwable $e) {
                    // evita tronar si ya existe FK
                }
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('servicio')) return;

        Schema::table('servicio', function (Blueprint $table) {
            try { $table->dropForeign(['orden_id']); } catch (\Throwable $e) {}

            if (Schema::hasColumn('servicio', 'orden_validada_at')) $table->dropColumn('orden_validada_at');
            if (Schema::hasColumn('servicio', 'orden_id')) $table->dropColumn('orden_id');
            if (Schema::hasColumn('servicio', 'mantenimiento_tipo')) $table->dropColumn('mantenimiento_tipo');

            // estado_proceso NO lo bajamos si ya lo usas en tu sistema, pero si quieres:
            // if (Schema::hasColumn('servicio', 'estado_proceso')) $table->dropColumn('estado_proceso');
        });
    }
};