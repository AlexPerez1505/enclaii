<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID único
            $table->string('nomina')->unique(); // Campo para el número de nómina
            $table->string('password'); // Contraseña
            $table->string('email')->unique()->nullable(); // Email (opcional)
            $table->string('name')->nullable(); // Nombre del usuario (opcional)
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}

