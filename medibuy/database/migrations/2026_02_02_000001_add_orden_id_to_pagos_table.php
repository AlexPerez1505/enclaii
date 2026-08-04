<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos', 'orden_id')) {
                $table->unsignedBigInteger('orden_id')->nullable()->after('venta_id');

                $table->index('orden_id');
                $table->foreign('orden_id')->references('id')->on('ordenes')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'orden_id')) {
                $table->dropForeign(['orden_id']);
                $table->dropIndex(['orden_id']);
                $table->dropColumn('orden_id');
            }
        });
    }
};
