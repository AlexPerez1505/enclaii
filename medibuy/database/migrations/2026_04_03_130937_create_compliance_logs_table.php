<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->string('equipment_name');
            $table->string('serial_number')->nullable();
            $table->foreignId('rental_id')->nullable()->constrained('rentals')->nullOnDelete();
            $table->string('client_name')->nullable();
            $table->enum('event_type', [
                'Uso en cirugía',
                'Entrega',
                'Recolección',
                'Mantenimiento',
                'Calibración',
                'Falla',
                'Incidente',
            ]);
            $table->date('date');
            $table->string('responsible')->nullable();
            $table->text('description')->nullable();
            $table->boolean('maintenance_valid')->default(true);
            $table->string('document_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_logs');
    }
};