<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();

            // Ajustado a tu tabla real: clientes
            $table->unsignedBigInteger('cliente_id');
            $table->string('cliente_nombre');

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->enum('service_type', [
                'Cirugía',
                'Evento programado',
                'Urgencia',
                'Renta prolongada'
            ]);

            $table->string('service_location')->nullable();
            $table->string('responsible')->nullable();

            $table->enum('status', [
                'Programada',
                'En curso',
                'Finalizada',
                'Cancelada'
            ])->default('Programada');

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};