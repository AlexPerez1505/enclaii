<?php
// database/migrations/2025_11_29_000000_add_orden_to_paquete_producto.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paquete_producto', function (Blueprint $table) {
            if (!Schema::hasColumn('paquete_producto', 'orden')) {
                $table->unsignedTinyInteger('orden')
                    ->default(0)
                    ->after('producto_id'); // la pone después del producto
            }
        });
    }

    public function down(): void
    {
        Schema::table('paquete_producto', function (Blueprint $table) {
            if (Schema::hasColumn('paquete_producto', 'orden')) {
                $table->dropColumn('orden');
            }
        });
    }
};
