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
       Schema::create('evidencias', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('checklist_id');
        $table->unsignedBigInteger('user_id');
        $table->string('archivo'); // path o nombre de archivo
        $table->string('tipo')->nullable(); // foto, pdf, otro
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
        Schema::dropIfExists('evidencias');
    }
};
