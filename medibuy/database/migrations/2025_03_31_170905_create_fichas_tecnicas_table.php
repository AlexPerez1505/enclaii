<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fichas_tecnicas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre de la ficha
            $table->string('archivo'); // Ruta del archivo PDF
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fichas_tecnicas');
    }
};
