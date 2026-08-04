<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCamionetasTable extends Migration
{
    public function up()
    {
        Schema::create('camionetas', function (Blueprint $table) {
            $table->id();
            $table->string('placa');
            $table->string('vin');
            $table->string('marca');
            $table->string('modelo');
            $table->year('anio');
            $table->string('color');
            $table->string('tipo_motor')->nullable();
            $table->string('capacidad_carga')->nullable();
            $table->string('tipo_combustible')->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->date('ultimo_mantenimiento')->nullable();
            $table->date('proximo_mantenimiento')->nullable();
            $table->date('ultima_verificacion')->nullable();
            $table->date('proxima_verificacion')->nullable();
            $table->integer('kilometraje')->nullable();
            $table->decimal('rendimiento_litro', 8, 2)->nullable();
            $table->decimal('costo_llenado', 10, 2)->nullable();
            $table->text('fotos')->nullable(); // Si almacenas las fotos como URLs
            $table->string('tarjeta_circulacion')->nullable();
            $table->string('verificacion')->nullable();
            $table->string('tenencia')->nullable();
            $table->string('seguro')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('camionetas');
    }
}
