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
        Schema::table('checklists', function (Blueprint $table) {
            // Eliminar foreign key de user_id si existe
            if (Schema::hasColumn('checklists', 'user_id')) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Si ya no existe el foreign key, continúa
                }
                $table->dropColumn('user_id');
            }

            // Eliminar todos los campos específicos de etapas si existen
            $fields = [
                'ingenieria_data',
                'ingenieria_firma_responsable',
                'ingenieria_firma_supervisor',
                'ingenieria_evidencias',
                'embalaje_data',
                'embalaje_firma_responsable',
                'embalaje_firma_supervisor',
                'embalaje_evidencias',
                'entrega_data',
                'tipo_entrega',
            ];

            foreach ($fields as $field) {
                if (Schema::hasColumn('checklists', $field)) {
                    $table->dropColumn($field);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            // Si deseas, puedes recrear las columnas aquí en caso de rollback
            // $table->json('ingenieria_data')->nullable();
            // $table->string('ingenieria_firma_responsable')->nullable();
            // ...
        });
    }
};
