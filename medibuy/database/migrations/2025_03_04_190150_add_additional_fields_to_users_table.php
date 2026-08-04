<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToUsersTable extends Migration
{
    /**
     * Ejecuta las modificaciones en la base de datos.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('curp')->nullable(); // CURP
            $table->string('ine')->nullable(); // INE
            $table->string('acta_nacimiento')->nullable(); // Acta de nacimiento
            $table->string('domicilio')->nullable(); // Domicilio
            $table->date('fecha_ingreso')->nullable(); // Fecha de ingreso
            $table->integer('faltas')->default(0); // Faltas
            $table->integer('asistencias')->default(0); // Asistencias
            $table->integer('permisos_utilizados')->default(0); // Permisos utilizados
            $table->string('nombre_contacto_emergencia')->nullable(); // Nombre de contacto de emergencia
            $table->string('numero_contacto_emergencia')->nullable(); // Número de contacto de emergencia
            $table->string('domicilio_contacto_emergencia')->nullable(); // Domicilio de contacto de emergencia
            $table->string('nombre_contacto_emergencia_secundario')->nullable(); // Nombre de contacto de emergencia secundario
            $table->string('numero_contacto_emergencia_secundario')->nullable(); // Número de contacto de emergencia secundario
            $table->string('domicilio_contacto_emergencia_secundario')->nullable(); // Domicilio de contacto de emergencia secundario
            $table->string('licencia')->nullable(); // Licencia
        });
    }

    /**
     * Revertir las modificaciones en la base de datos.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'curp',
                'ine',
                'acta_nacimiento',
                'domicilio',
                'fecha_ingreso',
                'faltas',
                'asistencias',
                'permisos_utilizados',
                'nombre_contacto_emergencia',
                'numero_contacto_emergencia',
                'domicilio_contacto_emergencia',
                'nombre_contacto_emergencia_secundario',
                'numero_contacto_emergencia_secundario',
                'domicilio_contacto_emergencia_secundario',
                'licencia'
            ]);
        });
    }
}
