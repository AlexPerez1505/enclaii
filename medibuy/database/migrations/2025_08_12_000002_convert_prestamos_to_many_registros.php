<?php
// database/migrations/2025_08_12_000002_convert_prestamos_to_many_registros.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Pivote
        if (!Schema::hasTable('prestamo_registro')) {
            Schema::create('prestamo_registro', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prestamo_id')->constrained()->cascadeOnDelete();
                $table->foreignId('registro_id')->constrained()->restrictOnDelete();
                $table->timestamps();
                $table->unique(['prestamo_id','registro_id']);
            });
        }

        // 2) Backfill desde prestamos.registro_id (si existe)
        if (Schema::hasColumn('prestamos', 'registro_id')) {
            DB::statement("
                INSERT INTO prestamo_registro (prestamo_id, registro_id, created_at, updated_at)
                SELECT id, registro_id, NOW(), NOW()
                FROM prestamos
                WHERE registro_id IS NOT NULL
            ");
        }

        // 3) Quitar FK y columna registro_id
        if (Schema::hasColumn('prestamos', 'registro_id')) {
            Schema::table('prestamos', function (Blueprint $table) {
                // intenta soltar la fk con el nombre por convención
                try { $table->dropForeign(['registro_id']); } catch (\Throwable $e) {}
                $table->dropColumn('registro_id');
            });
        }
    }

    public function down(): void
    {
        // Restituir registro_id (nullable) y opcionalmente regresar un valor cualquiera
        if (!Schema::hasColumn('prestamos', 'registro_id')) {
            Schema::table('prestamos', function (Blueprint $table) {
                $table->unsignedBigInteger('registro_id')->nullable()->after('id');
                $table->foreign('registro_id')->references('id')->on('registros')->nullOnDelete();
            });

            // opcional: traer un registro (mínimo) por préstamo desde la pivote
            DB::statement("
                UPDATE prestamos p
                JOIN (
                    SELECT prestamo_id, MIN(registro_id) AS registro_id
                    FROM prestamo_registro
                    GROUP BY prestamo_id
                ) x ON x.prestamo_id = p.id
                SET p.registro_id = x.registro_id
            ");
        }
        // no borramos la pivote en down para no perder datos
    }
};
