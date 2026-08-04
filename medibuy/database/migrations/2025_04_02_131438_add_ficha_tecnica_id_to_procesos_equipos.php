<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('procesos_equipos', function (Blueprint $table) {
            $table->foreignId('ficha_tecnica_id')
                ->nullable()
                ->constrained('fichas_tecnicas')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('procesos_equipos', function (Blueprint $table) {
            $table->dropForeign(['ficha_tecnica_id']);
            $table->dropColumn('ficha_tecnica_id');
        });
    }
};
