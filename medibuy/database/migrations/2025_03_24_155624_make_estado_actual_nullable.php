<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->integer('estado_actual')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->integer('estado_actual')->nullable(false)->change();
        });
    }
};
