<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenChecklistItemTable extends Migration
{
    public function up()
    {
        Schema::create('orden_checklist_item', function (Blueprint $table) {
            // 1) Definimos las columnas como unsignedBigInteger
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('checklist_item_id');

            // 2) Clave foránea a ordenes.id
            $table->foreign('orden_id')
                  ->references('id')
                  ->on('ordenes')
                  ->onDelete('cascade');

            // 3) Clave foránea a checklist_items.id
            $table->foreign('checklist_item_id')
                  ->references('id')
                  ->on('checklist_items')
                  ->onDelete('cascade');

            // 4) Primary compuesto
            $table->primary(['orden_id', 'checklist_item_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_checklist_item');
    }
}
