<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('rental_id');
            $table->unsignedBigInteger('cliente_id');
            $table->string('cliente_nombre');

            $table->string('invoice_number')->unique();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->enum('payment_status', [
                'Pendiente',
                'Pagado',
                'Parcial',
                'Vencido',
            ])->default('Pendiente');

            $table->enum('payment_method', [
                'Transferencia',
                'Efectivo',
                'Tarjeta',
                'Cheque',
                'Otro',
            ])->nullable();

            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('rental_id')
                ->references('id')
                ->on('rentals')
                ->cascadeOnDelete();

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};