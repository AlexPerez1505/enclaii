<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_recordatorios', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id(); // BIGINT UNSIGNED
            // FK AL NOMBRE CORRECTO: 'pagos_financiamiento'
            $table->foreignId('pago_financiamiento_id')
                  ->constrained('pagos_financiamiento')
                  ->cascadeOnDelete();

            // Canal/etapa que usa tu comando
            $table->enum('channel', ['email','whatsapp'])->index();
            $table->string('stage', 20)->index(); // 'pre7', 'due', 'overdue'

            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();

            $table->index(['pago_financiamiento_id', 'channel', 'stage'], 'pr_idx_pago_channel_stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_recordatorios');
    }
};
