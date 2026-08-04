<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('venta_productos', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_productos', 'accesorios')) {
                $table->text('accesorios')->nullable()->after('registro_id');
            }
            if (!Schema::hasColumn('venta_productos', 'notas')) {
                $table->text('notas')->nullable()->after('accesorios');
            }
        });
    }

    public function down(): void
    {
        Schema::table('venta_productos', function (Blueprint $table) {
            if (Schema::hasColumn('venta_productos', 'notas')) {
                $table->dropColumn('notas');
            }
            if (Schema::hasColumn('venta_productos', 'accesorios')) {
                $table->dropColumn('accesorios');
            }
        });
    }
};