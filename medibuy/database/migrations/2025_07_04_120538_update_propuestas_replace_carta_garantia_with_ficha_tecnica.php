<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('propuestas', function (Blueprint $table) {
            if (Schema::hasColumn('propuestas', 'carta_garantia_id')) {
                $table->dropForeign(['carta_garantia_id']);
                $table->dropColumn('carta_garantia_id');
            }

            $table->unsignedBigInteger('ficha_tecnica_id')->nullable()->after('plan');
            $table->foreign('ficha_tecnica_id')->references('id')->on('fichas_tecnicas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('propuestas', function (Blueprint $table) {
            $table->dropForeign(['ficha_tecnica_id']);
            $table->dropColumn('ficha_tecnica_id');

            $table->unsignedBigInteger('carta_garantia_id')->nullable()->after('plan');
            $table->foreign('carta_garantia_id')->references('id')->on('carta_garantias')->onDelete('set null');
        });
    }
};
