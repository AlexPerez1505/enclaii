<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable(); // Teléfono
            $table->string('imagen')->nullable(); // Foto de perfil
            $table->string('cargo')->nullable(); // Cargo
            $table->string('puesto')->nullable(); // Puesto
            $table->integer('vacaciones_disponibles')->default(0); // Vacaciones disponibles
            $table->integer('vacaciones_utilizadas')->default(0); // Vacaciones usadas
            $table->integer('permisos')->default(0); // Permisos
            $table->integer('retardos')->default(0); // Retardos
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'imagen', 'cargo', 'puesto', 'vacaciones_disponibles', 'vacaciones_utilizadas', 'permisos', 'retardos']);
        });
    }
}
