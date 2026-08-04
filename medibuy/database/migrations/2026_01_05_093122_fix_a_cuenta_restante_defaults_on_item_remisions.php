<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('item_remisions', function (Blueprint $table) {
            // Si existen, los hacemos seguros para el nuevo flujo (sin anticipo)
            if (Schema::hasColumn('item_remisions', 'a_cuenta')) {
                $table->decimal('a_cuenta', 12, 2)->default(0)->nullable()->change();
            }
            if (Schema::hasColumn('item_remisions', 'restante')) {
                $table->decimal('restante', 12, 2)->default(0)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // No lo revertimos a NOT NULL para no romper producción
    }
};
