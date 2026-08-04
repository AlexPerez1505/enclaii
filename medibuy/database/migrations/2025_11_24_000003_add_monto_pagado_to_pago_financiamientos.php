<?php 
// database/migrations/2025_11_24_000003_add_monto_pagado_to_pagos_financiamiento.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pagos_financiamiento', function (Blueprint $t) {
            if (!Schema::hasColumn('pagos_financiamiento', 'monto_pagado')) {
                $t->decimal('monto_pagado', 14, 2)
                    ->default(0)
                    ->after('monto');
            }

            // Asegurar default del campo pagado (solo si existe)
            if (Schema::hasColumn('pagos_financiamiento', 'pagado')) {
                $t->boolean('pagado')->default(false)->change();
            }
        });
    }

    public function down(): void {
        Schema::table('pagos_financiamiento', function (Blueprint $t) {
            if (Schema::hasColumn('pagos_financiamiento', 'monto_pagado')) {
                $t->dropColumn('monto_pagado');
            }
        });
    }
};
