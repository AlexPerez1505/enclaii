<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();

            // Polimórfico (Ticket o TicketComment)
            // OJO: nullableMorphs YA crea el índice para (attachable_type, attachable_id)
            $table->nullableMorphs('attachable'); // attachable_type, attachable_id + index

            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();

            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('sha256', 64)->nullable();

            $table->timestamps();

            // ❌ NO volver a indexar porque ya lo creó nullableMorphs()
            // $table->index(['attachable_type', 'attachable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};  