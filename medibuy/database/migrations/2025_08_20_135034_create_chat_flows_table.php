<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_flows', function (Blueprint $table) {
            $table->id();
            $table->string('from');              // número del cliente (E.164)
            $table->string('step')->default('start'); // p.ej. start, menu, cotizar_equipo, etc.
            $table->json('context')->nullable(); // datos extra: equipo, folio, etc.
            $table->timestamps();

            $table->unique('from');              // 1 flujo activo por número
            $table->index('step');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_flows');
    }
};
