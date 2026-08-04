<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Columnas nuevas para "vendido"
        Schema::table('prestamo_registro', function (Blueprint $table) {
            if (!Schema::hasColumn('prestamo_registro', 'vendido_scanned_at')) {
                $table->dateTime('vendido_scanned_at')->nullable()->after('devolucion_scanned_by');
            }
            if (!Schema::hasColumn('prestamo_registro', 'vendido_scanned_by')) {
                $table->unsignedBigInteger('vendido_scanned_by')->nullable()->after('vendido_scanned_at');
            }
        });

        // 2) Índices (usamos nombres explícitos para poder gestionarlos en down())
        Schema::table('prestamo_registro', function (Blueprint $table) {
            // Evitar duplicados del mismo registro en el mismo préstamo
            $table->unique(['prestamo_id', 'registro_id'], 'uq_prestamo_registro');

            // Índices de consulta frecuentes
            $table->index('prestamo_id', 'idx_pr_prestamo');
            $table->index('registro_id', 'idx_pr_registro');
            if (Schema::hasColumn('prestamo_registro', 'salida_scanned_at')) {
                $table->index('salida_scanned_at', 'idx_pr_salida_at');
            }
            if (Schema::hasColumn('prestamo_registro', 'devolucion_scanned_at')) {
                $table->index('devolucion_scanned_at', 'idx_pr_devolucion_at');
            }
            if (Schema::hasColumn('prestamo_registro', 'vendido_scanned_at')) {
                $table->index('vendido_scanned_at', 'idx_pr_vendido_at');
            }
        });

        // 3) Claves foráneas (si existen tablas/columnas; si falla, ignoramos sin romper deploy)
        try {
            Schema::table('prestamo_registro', function (Blueprint $table) {
                // a) FKs principales
                $table->foreign('prestamo_id', 'fk_pr_prestamo')
                    ->references('id')->on('prestamos')->onDelete('cascade');

                $table->foreign('registro_id', 'fk_pr_registro')
                    ->references('id')->on('registros')->onDelete('cascade');

                // b) FKs a users para los *_by (pueden no existir; por eso el try/catch)
                if (Schema::hasColumn('prestamo_registro', 'salida_scanned_by')) {
                    $table->foreign('salida_scanned_by', 'fk_pr_salida_by')
                        ->references('id')->on('users')->nullOnDelete();
                }
                if (Schema::hasColumn('prestamo_registro', 'devolucion_scanned_by')) {
                    $table->foreign('devolucion_scanned_by', 'fk_pr_devol_by')
                        ->references('id')->on('users')->nullOnDelete();
                }
                if (Schema::hasColumn('prestamo_registro', 'vendido_scanned_by')) {
                    $table->foreign('vendido_scanned_by', 'fk_pr_vend_by')
                        ->references('id')->on('users')->nullOnDelete();
                }
            });
        } catch (\Throwable $e) {
            // Si no existe la tabla users o ya existen las FKs, seguimos sin romper la migración
            // Log::warning('FK creation skipped in prestamo_registro: '.$e->getMessage());
        }
    }

    public function down(): void
    {
        // Primero soltar FKs e índices, luego columnas
        try {
            Schema::table('prestamo_registro', function (Blueprint $table) {
                // Soltar FKs si existen
                foreach (['fk_pr_prestamo', 'fk_pr_registro', 'fk_pr_salida_by', 'fk_pr_devol_by', 'fk_pr_vend_by'] as $fk) {
                    try { $table->dropForeign($fk); } catch (\Throwable $e) {}
                }

                // Soltar índices
                foreach ([
                    'uq_prestamo_registro',
                    'idx_pr_prestamo',
                    'idx_pr_registro',
                    'idx_pr_salida_at',
                    'idx_pr_devolucion_at',
                    'idx_pr_vendido_at',
                ] as $idx) {
                    try { $table->dropIndex($idx); } catch (\Throwable $e) {}
                    try { $table->dropUnique($idx); } catch (\Throwable $e) {}
                }
            });
        } catch (\Throwable $e) {
            // Ignorar
        }

        Schema::table('prestamo_registro', function (Blueprint $table) {
            if (Schema::hasColumn('prestamo_registro', 'vendido_scanned_by')) {
                $table->dropColumn('vendido_scanned_by');
            }
            if (Schema::hasColumn('prestamo_registro', 'vendido_scanned_at')) {
                $table->dropColumn('vendido_scanned_at');
            }
        });
    }
};
