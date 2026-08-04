<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $t) {
            if (!Schema::hasColumn('ordenes', 'mto_preventivo')) {
                $t->json('mto_preventivo')->nullable();
            }
            if (!Schema::hasColumn('ordenes', 'mto_realizado')) {
                $t->json('mto_realizado')->nullable();
            }
            // Si existe una columna vieja 'checklist', puedes dejarla o dropearla:
            // if (Schema::hasColumn('ordenes','checklist')) { $t->dropColumn('checklist'); }
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $t) {
            if (Schema::hasColumn('ordenes', 'mto_preventivo')) $t->dropColumn('mto_preventivo');
            if (Schema::hasColumn('ordenes', 'mto_realizado'))  $t->dropColumn('mto_realizado');
        });
    }
};
