<?php
// database/migrations/2025_07_23_000002_create_checklist_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistItemsTable extends Migration
{
    public function up()
    {
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();

            // 1) Definimos manualmente los campos FK
            $table->unsignedBigInteger('aparato_id');
            $table->unsignedBigInteger('checklist_category_id');

            $table->string('nombre');    // e.g. "Conector de luz"
            $table->string('resultado'); // e.g. "Bueno y Funcional"
            $table->timestamps();

            // 2) Clave foránea a aparatos.id
            $table->foreign('aparato_id')
                  ->references('id')
                  ->on('aparatos')
                  ->onDelete('cascade');

            // 3) Clave foránea a checklist_categories.id
            $table->foreign('checklist_category_id')
                  ->references('id')
                  ->on('checklist_categories')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('checklist_items');
    }
}
