<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registros', function (Blueprint $table) {
            if (!Schema::hasColumn('registros', 'devolucion_scanned_at')) {
                $table->timestamp('devolucion_scanned_at')->nullable()->after('updated_at');
            }

            if (!Schema::hasColumn('registros', 'vendido_scanned_at')) {
                $table->timestamp('vendido_scanned_at')->nullable()->after('devolucion_scanned_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registros', function (Blueprint $table) {
            if (Schema::hasColumn('registros', 'vendido_scanned_at')) {
                $table->dropColumn('vendido_scanned_at');
            }

            if (Schema::hasColumn('registros', 'devolucion_scanned_at')) {
                $table->dropColumn('devolucion_scanned_at');
            }
        });
    }
};