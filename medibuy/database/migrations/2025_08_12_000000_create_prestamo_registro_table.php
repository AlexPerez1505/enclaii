<?php
// database/migrations/2025_08_12_000000_create_prestamo_registro_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prestamo_registro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('registro_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['prestamo_id','registro_id']); // evita duplicados
        });
        // Opcional: si tu tabla prestamos tiene registro_id, puedes dejarlo nullable o eliminarlo en otra migración
        // Schema::table('prestamos', fn (Blueprint $t) => $t->dropConstrainedForeignId('registro_id'));
    }
    public function down(): void {
        Schema::dropIfExists('prestamo_registro');
    }
};
