<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvidenciaToRegistrosTable extends Migration
{
    public function up()
    {
        Schema::table('registros', function (Blueprint $table) {
            // Verificar si la columna 'evidencia' existe antes de eliminarla
            if (Schema::hasColumn('registros', 'evidencia')) {
                $table->dropColumn('evidencia');
            }

            // Agregar nuevas columnas en lugar del JSON
            $table->string('evidencia1')->nullable();
            $table->string('evidencia2')->nullable();
            $table->string('evidencia3')->nullable();
            $table->string('user_name'); // Almacena el nombre del usuario
        });
    }

    public function down()
    {
        Schema::table('registros', function (Blueprint $table) {
            // Eliminar las nuevas columnas
            $table->dropColumn(['evidencia1', 'evidencia2', 'evidencia3', 'user_name']);

            // Restaurar la columna evidencia solo si no existe
            if (!Schema::hasColumn('registros', 'evidencia')) {
                $table->text('evidencia')->nullable();
            }
        });
    }
}
