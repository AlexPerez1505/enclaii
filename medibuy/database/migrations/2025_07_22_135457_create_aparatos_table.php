<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAparatosTable extends Migration
{
    public function up()
    {
        Schema::create('aparatos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->index();
            $table->string('modelo')->nullable();
            $table->string('marca')->nullable();
            $table->integer('stock')->default(1);
            $table->decimal('precio', 12, 2)->default(0);
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aparatos');
    }
}
