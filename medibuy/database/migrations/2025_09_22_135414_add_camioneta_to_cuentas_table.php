<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuentas', function (Blueprint $table) {
            if (!Schema::hasColumn('cuentas', 'camioneta')) {
                $table->string('camioneta', 100)->nullable()->after('lugar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cuentas', function (Blueprint $table) {
            if (Schema::hasColumn('cuentas', 'camioneta')) {
                $table->dropColumn('camioneta');
            }
        });
    }
};
