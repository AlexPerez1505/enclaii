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
        Schema::create('checklist_firmas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('checklist_id');
        $table->unsignedBigInteger('user_id');
        $table->enum('rol', ['responsable', 'supervisor', 'entregador', 'receptor']);
        $table->string('firma'); // Puede ser base64, path o nombre de archivo
        $table->timestamp('fecha_firma');
        $table->timestamps();

        $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_firmas');
    }
};
