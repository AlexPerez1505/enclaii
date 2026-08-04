<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No rompemos si ya existe
        if (!Schema::hasTable('ticket_user')) {
            Schema::create('ticket_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['ticket_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_user');
    }
};
