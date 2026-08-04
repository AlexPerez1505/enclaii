<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('checklist_recepciones', function (Blueprint $table) {
        $table->id();
        $table->foreignId('checklist_id')->constrained()->onDelete('cascade');
        $table->string('nombre_responsable');
        $table->json('checklist'); // checklist recibido (json)
        $table->text('observaciones')->nullable();
        $table->string('firma_recepcion');
        $table->json('evidencias')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_recepcions');
    }
};
