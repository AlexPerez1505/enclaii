<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('ficha_tecnica_id')->nullable()->after('id');
            $table->foreign('ficha_tecnica_id')->references('id')->on('fichas_tecnicas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->dropForeign(['ficha_tecnica_id']);
            $table->dropColumn('ficha_tecnica_id');
        });
    }
};
