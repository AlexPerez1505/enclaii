<?php
// database/migrations/2025_09_22_000002_add_return_columns_to_prestamo_registro.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('prestamo_registro', function (Blueprint $table) {
            if (!Schema::hasColumn('prestamo_registro','devolucion_scanned_at')) {
                $table->timestamp('devolucion_scanned_at')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('prestamo_registro','devolucion_scanned_by')) {
                $table->unsignedBigInteger('devolucion_scanned_by')->nullable()->after('devolucion_scanned_at');
                // $table->foreign('devolucion_scanned_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }
    public function down(): void {
        Schema::table('prestamo_registro', function (Blueprint $table) {
            if (Schema::hasColumn('prestamo_registro','devolucion_scanned_by')) {
                // $table->dropForeign(['devolucion_scanned_by']);
                $table->dropColumn('devolucion_scanned_by');
            }
            if (Schema::hasColumn('prestamo_registro','devolucion_scanned_at')) {
                $table->dropColumn('devolucion_scanned_at');
            }
        });
    }
};
