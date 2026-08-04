<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropChecklistColumnFromOrdenesTable extends Migration
{
    public function up()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn('checklist');
        });
    }

    public function down()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // Si quisieras revertir, recrea la columna como JSON o text:
            $table->text('checklist')->nullable();
        });
    }
}
