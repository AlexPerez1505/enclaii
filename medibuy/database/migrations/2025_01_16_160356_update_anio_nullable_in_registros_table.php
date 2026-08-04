<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAnioNullableInRegistrosTable extends Migration
{
    public function up()
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->year('anio')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->year('anio')->nullable(false)->change();
        });
    }
}

