<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('venta_productos', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_productos', 'is_regalo')) {
                $table->boolean('is_regalo')->default(false)->after('registro_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('venta_productos', function (Blueprint $table) {
            if (Schema::hasColumn('venta_productos', 'is_regalo')) {
                $table->dropColumn('is_regalo');
            }
        });
    }
};