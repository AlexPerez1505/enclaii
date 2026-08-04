<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // Quitamos columnas que ya no usaremos
            $table->dropColumn(['alert', 'url']);

            // Campos nuevos para recordatorios
            $table->string('timezone', 80)
                  ->default('America/Mexico_City')
                  ->after('repeat_end');

            $table->integer('remind_offset_minutes')
                  ->default(60)
                  ->after('timezone');

            $table->json('wpp')
                  ->nullable()
                  ->after('remind_offset_minutes');

            $table->timestamp('next_reminder_at')
                  ->nullable()
                  ->after('wpp');

            $table->timestamp('last_reminded_at')
                  ->nullable()
                  ->after('next_reminder_at');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // Revertir: quitamos los nuevos
            $table->dropColumn([
                'timezone',
                'remind_offset_minutes',
                'wpp',
                'next_reminder_at',
                'last_reminded_at',
            ]);

            // Volvemos a crear los que borramos
            $table->string('alert')->nullable();
            $table->string('url', 1024)->nullable();
        });
    }
};
