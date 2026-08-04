<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_events', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();

            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();

            $table->integer('remind_offset_minutes')->nullable();
            $table->string('repeat_rule')->default('none'); // none, daily, weekly, monthly
            $table->string('timezone')->nullable();

            $table->json('user_ids')->nullable();

            $table->boolean('send_email')->default(false);
            $table->boolean('send_whatsapp')->default(false);

            $table->boolean('all_day')->default(false);
            $table->boolean('completed')->default(false);

            $table->string('color')->nullable();
            $table->string('category')->nullable();
            $table->string('priority')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();

            $table->dateTime('next_reminder_at')->nullable();
            $table->dateTime('last_reminder_sent_at')->nullable();

            $table->timestamps();

            // Índices útiles para el scheduler de recordatorios
            $table->index('next_reminder_at');
            $table->index('completed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_events');
    }
};