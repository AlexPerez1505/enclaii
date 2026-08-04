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
         Schema::create('incidentes', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('checklist_id');
        $table->unsignedBigInteger('item_parte_id')->nullable();
        $table->unsignedBigInteger('user_id');
        $table->text('descripcion');
        $table->string('tipo')->nullable(); // daño, falta, otro
        $table->timestamps();

        $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
        $table->foreign('item_parte_id')->references('id')->on('item_partes')->onDelete('set null');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidentes');
    }
};
