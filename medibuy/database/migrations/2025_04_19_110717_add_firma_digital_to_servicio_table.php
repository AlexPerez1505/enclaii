<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirmaDigitalToServicioTable extends Migration
{
    public function up(): void
    {
        Schema::table('servicio', function (Blueprint $table) {
            $table->text('firma_digital')->nullable()->after('observaciones');
        });
    }

    public function down(): void
    {
        Schema::table('servicio', function (Blueprint $table) {
            $table->dropColumn('firma_digital');
        });
    }
}
