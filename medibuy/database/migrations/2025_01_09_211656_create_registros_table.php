<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrosTable extends Migration
{
    public function up()
    {
        Schema::create('registros', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_equipo');
            $table->string('subtipo_equipo')->nullable();
            $table->string('subtipo_equipo_otro')->nullable();
            $table->string('numero_serie');
            $table->string('marca');
            $table->string('modelo');
            $table->year('anio');
            $table->text('descripcion');
            $table->integer('estado_actual'); // ID_movimientoActivo
            $table->date('fecha_adquisicion');
            $table->date('ultimo_mantenimiento')->nullable();
            $table->date('proximo_mantenimiento')->nullable();

            // En lugar de almacenar un JSON, ahora tenemos 3 columnas separadas
            $table->string('evidencia1')->nullable();
            $table->string('evidencia2')->nullable();
            $table->string('evidencia3')->nullable();

            $table->string('video')->nullable(); // Almacena la URL del video
            $table->string('documentoPDF')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('firma_digital')->nullable(); // URL de la firma

            // Columna para almacenar el nombre del usuario que crea el registro
            $table->string('user_name'); // Almacena el nombre del usuario
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registros');
    }
}
