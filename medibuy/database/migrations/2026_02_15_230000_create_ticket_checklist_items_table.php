<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_checklist_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();

            // Para mantener orden del checklist original
            $table->unsignedInteger('position')->default(0);

            // Definición del ítem (lo genera IA)
            $table->string('text', 500);
            $table->boolean('required')->default(false);
            $table->boolean('evidence_required')->default(false);
            $table->json('evidence_types')->nullable();

            // Estado
            $table->enum('status', ['pending','in_progress','done','blocked'])->default('pending');

            // Última actualización (bitácora)
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('updated_at_action')->nullable();

            // Campos de avance más recientes (último update)
            $table->text('what_done')->nullable();      // qué hicieron
            $table->text('how_done')->nullable();       // cómo
            $table->text('observations')->nullable();   // observaciones/bloqueos

            $table->timestamps();

            $table->index(['ticket_id','position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_checklist_items');
    }
};