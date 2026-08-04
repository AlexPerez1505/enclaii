<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->string('equipment_name');
            $table->enum('type', ['Preventivo', 'Correctivo']);
            $table->date('date');
            $table->date('next_maintenance_date')->nullable();
            $table->string('technician')->nullable();
            $table->text('description')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->enum('equipment_status_after', ['Disponible', 'Mantenimiento', 'Fuera de servicio'])->nullable();
            $table->string('calibration_certificate_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};