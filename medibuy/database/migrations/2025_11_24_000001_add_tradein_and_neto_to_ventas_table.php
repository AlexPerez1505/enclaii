<?php
// database/migrations/2025_11_24_000001_add_tradein_and_neto_to_ventas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('ventas', function (Blueprint $t) {
      $t->decimal('total_original', 14, 2)->nullable()->after('iva');
      $t->decimal('tradein_total', 14, 2)->default(0)->after('total_original');
      $t->decimal('total_neto', 14, 2)->nullable()->after('tradein_total');
    });
  }
  public function down(): void {
    Schema::table('ventas', function (Blueprint $t) {
      $t->dropColumn(['total_original','tradein_total','total_neto']);
    });
  }
};
