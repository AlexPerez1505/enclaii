<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->unique();
            $table->year('year_of_manufacture')->nullable();
            $table->enum('status', ['Disponible', 'Rentado', 'Mantenimiento', 'Fuera de servicio'])->default('Disponible');
            $table->string('current_location')->nullable();
            $table->decimal('equipment_cost', 12, 2)->default(0);
            $table->decimal('rental_price_day', 12, 2)->default(0);
            $table->decimal('rental_price_event', 12, 2)->default(0);
            $table->unsignedInteger('useful_life_years')->nullable();
            $table->string('photo_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};