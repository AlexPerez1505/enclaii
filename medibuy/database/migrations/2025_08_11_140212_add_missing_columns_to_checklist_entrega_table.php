<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Crear tabla si no existe
        if (!Schema::hasTable('checklist_entrega')) {
            Schema::create('checklist_entrega', function (Blueprint $table) {
                $table->id();

                $table->foreignId('checklist_id')
                    ->constrained('checklists')
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                // Columnas que solicitaste
                $table->json('datos_entrega')->nullable();
                $table->text('observaciones')->nullable();
                $table->string('firma_cliente')->nullable();
                $table->string('firma_entrega')->nullable();
                $table->json('evidencias')->nullable();
                $table->json('verificados')->nullable();
                $table->json('no_verificados')->nullable();
                $table->json('componentes')->nullable();

                $table->timestamps();

                $table->index('checklist_id');
                $table->index('user_id');
            });
        } else {
            // Si existe, solo agregar columnas faltantes
            Schema::table('checklist_entrega', function (Blueprint $table) {
                if (!Schema::hasColumn('checklist_entrega', 'datos_entrega'))
                    $table->json('datos_entrega')->nullable()->after('user_id');

                if (!Schema::hasColumn('checklist_entrega', 'observaciones'))
                    $table->text('observaciones')->nullable()->after('datos_entrega');

                if (!Schema::hasColumn('checklist_entrega', 'firma_cliente'))
                    $table->string('firma_cliente')->nullable()->after('observaciones');

                if (!Schema::hasColumn('checklist_entrega', 'firma_entrega'))
                    $table->string('firma_entrega')->nullable()->after('firma_cliente');

                if (!Schema::hasColumn('checklist_entrega', 'evidencias'))
                    $table->json('evidencias')->nullable()->after('firma_entrega');

                if (!Schema::hasColumn('checklist_entrega', 'verificados'))
                    $table->json('verificados')->nullable()->after('evidencias');

                if (!Schema::hasColumn('checklist_entrega', 'no_verificados'))
                    $table->json('no_verificados')->nullable()->after('verificados');

                if (!Schema::hasColumn('checklist_entrega', 'componentes'))
                    $table->json('componentes')->nullable()->after('no_verificados');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_entrega');
    }
};
