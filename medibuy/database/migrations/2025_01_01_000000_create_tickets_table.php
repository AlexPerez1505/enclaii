<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['open','in_progress','resolved','closed'])->default('open');
            $table->enum('priority', ['low','medium','high','urgent'])->default('medium');
            // visibility: private => solo creador + asignados; shared => creador + asignados (igual que private, por compatibilidad);
            // public => todos los autenticados pueden verlo
            $table->enum('visibility', ['private','shared','public'])->default('private');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status','priority','visibility']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
