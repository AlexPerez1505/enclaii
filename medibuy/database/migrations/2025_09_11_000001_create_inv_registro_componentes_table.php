<?php
// database/migrations/2025_09_11_000001_create_inv_registro_componentes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inv_registro_componentes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('registro_id')->constrained('registros')->cascadeOnDelete();

            // Si tienes catálogo, este puede ser null:
            $t->unsignedBigInteger('componente_id')->nullable();

            // Guardamos el nombre que llegó (cacheado) para trazabilidad:
            $t->string('nombre_cache', 255);

            // Cantidad reportada (puede ser 0 si faltó)
            $t->unsignedInteger('cantidad')->default(0);

            // 1 = viene / 0 = faltante
            $t->boolean('incluido')->default(false);

            $t->string('notas', 255)->nullable();
            $t->timestamps();

            // Índices útiles
            $t->index(['registro_id', 'incluido']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('inv_registro_componentes');
    }
};
