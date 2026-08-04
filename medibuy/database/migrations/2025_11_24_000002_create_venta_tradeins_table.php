<?php
// database/migrations/2025_11_24_000002_create_venta_tradeins_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('venta_tradeins', function (Blueprint $t) {
      $t->id();
      $t->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();

      $t->string('tipo_equipo')->nullable();
      $t->string('marca')->nullable();
      $t->string('modelo')->nullable();
      $t->string('numero_serie')->nullable();
      $t->text('descripcion')->nullable();

      $t->decimal('valor_a_cuenta', 14, 2)->default(0);
      $t->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('venta_tradeins');
  }
};
