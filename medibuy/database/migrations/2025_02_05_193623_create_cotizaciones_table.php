<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('cliente');
            $table->json('productos'); // Guardar productos en formato JSON
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('tipo_pago');
            $table->json('plan_pagos')->nullable();
            $table->text('nota')->nullable();
            $table->date('valido_hasta');
            $table->string('lugar_cotizacion');
            $table->timestamps();
        });
    }
    
};
