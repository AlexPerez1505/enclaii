<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade campos para el flujo con QR:
     * - qr_token: identificador del enlace público (escaneado por el usuario)
     * - qr_expires_at: fecha/hora de expiración del QR
     * - acknowledged_at: cuándo el usuario aceptó/firmó la recepción
     */
    public function up(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_transactions', 'qr_token')) {
                // UUID cabe en 36, dejo 64 por si cambias a tokens más largos (firmados)
                $table->string('qr_token', 64)->nullable()->after('nip_approved_at');
                $table->timestamp('qr_expires_at')->nullable()->after('qr_token');
                $table->timestamp('acknowledged_at')->nullable()->after('qr_expires_at');

                // índice corto para evitar errores de "identifier too long"
                $table->index('qr_token', 'cash_trx_qr_token_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            // si ya se ejecutó alguna vez, este índice puede existir
            try { $table->dropIndex('cash_trx_qr_token_idx'); } catch (\Throwable $e) { /* ignore */ }

            if (Schema::hasColumn('cash_transactions', 'qr_token')) {
                $table->dropColumn(['qr_token', 'qr_expires_at', 'acknowledged_at']);
            }
        });
    }
};
