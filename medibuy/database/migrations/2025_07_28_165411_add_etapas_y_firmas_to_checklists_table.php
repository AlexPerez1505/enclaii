<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->json('ingenieria_data')->nullable()->after('finalizado');
            $table->string('ingenieria_firma_responsable')->nullable()->after('ingenieria_data');
            $table->string('ingenieria_firma_supervisor')->nullable()->after('ingenieria_firma_responsable');
            $table->json('ingenieria_evidencias')->nullable()->after('ingenieria_firma_supervisor');
            // Repite para embalaje y entrega si deseas...

            $table->json('embalaje_data')->nullable()->after('ingenieria_evidencias');
            $table->string('embalaje_firma_responsable')->nullable()->after('embalaje_data');
            $table->string('embalaje_firma_supervisor')->nullable()->after('embalaje_firma_responsable');
            $table->json('embalaje_evidencias')->nullable()->after('embalaje_firma_supervisor');

            // Si quieres: entrega_data, entrega_firma_responsable, etc...
        });
    }

    public function down(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropColumn([
                'ingenieria_data',
                'ingenieria_firma_responsable',
                'ingenieria_firma_supervisor',
                'ingenieria_evidencias',
                'embalaje_data',
                'embalaje_firma_responsable',
                'embalaje_firma_supervisor',
                'embalaje_evidencias'
                // etc
            ]);
        });
    }
};
