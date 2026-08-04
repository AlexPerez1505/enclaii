<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('checklist_categories', function (Blueprint $table) {
            // forzamos InnoDB
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->string('nombre')->unique(); // ej. 'conexiones','botones','componentes'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('checklist_categories');
    }
}
