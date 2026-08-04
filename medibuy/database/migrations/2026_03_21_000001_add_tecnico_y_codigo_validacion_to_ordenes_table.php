<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Orden;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ordenes', 'tecnico_id')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->foreignId('tecnico_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('ordenes', 'codigo_validacion_servicio')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->string('codigo_validacion_servicio', 30)
                    ->nullable()
                    ->after('tecnico_id');
            });
        }

        $ordenes = Orden::query()->whereNull('codigo_validacion_servicio')->get();

        foreach ($ordenes as $orden) {
            do {
                $codigo = 'SV-' . now()->format('ymd') . '-' . strtoupper(Str::random(6));
            } while (Orden::where('codigo_validacion_servicio', $codigo)->exists());

            $orden->codigo_validacion_servicio = $codigo;

            if (empty($orden->tecnico_id) && !empty($orden->user_id)) {
                $orden->tecnico_id = $orden->user_id;
            }

            $orden->save();
        }

        $hasUnique = collect(DB::select("SHOW INDEX FROM ordenes WHERE Key_name = 'ordenes_codigo_validacion_servicio_unique'"))->isNotEmpty();

        if (!$hasUnique) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->unique('codigo_validacion_servicio', 'ordenes_codigo_validacion_servicio_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ordenes', 'codigo_validacion_servicio')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->dropUnique('ordenes_codigo_validacion_servicio_unique');
            });
        }

        if (Schema::hasColumn('ordenes', 'codigo_validacion_servicio')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->dropColumn('codigo_validacion_servicio');
            });
        }

        if (Schema::hasColumn('ordenes', 'tecnico_id')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tecnico_id');
            });
        }
    }
};