<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('venta_productos', function (Blueprint $table) {
            $table->unsignedBigInteger('registro_id')->nullable()->after('producto_id');

            // Si quieres forzar integridad referencial (opcional):
            $table->foreign('registro_id')->references('id')->on('registros')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('venta_productos', function (Blueprint $table) {
            $table->dropForeign(['registro_id']);
            $table->dropColumn('registro_id');
        });
    }
};
