<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->unique()->constrained('rentals')->cascadeOnDelete();
            $table->string('rental_client')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->dateTime('pickup_date')->nullable();
            $table->string('driver')->nullable();
            $table->enum('status', ['Pendiente', 'En camino', 'Entregado', 'Recogido'])->default('Pendiente');
            $table->text('delivery_address')->nullable();
            $table->string('delivery_photo_url')->nullable();
            $table->string('pickup_photo_url')->nullable();
            $table->string('signature_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics');
    }
};