<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('checklists', function ($table) {
        // Elimina la foreign key primero (nombre por convención de Laravel)
        $table->dropForeign(['item_id']);
        // Ahora sí elimina la columna
        $table->dropColumn('item_id');
    });
}

public function down(): void
{
    Schema::table('checklists', function ($table) {
        $table->unsignedBigInteger('item_id')->nullable();
        // Si quieres, puedes agregar de nuevo la foreign key aquí:
        // $table->foreign('item_id')->references('id')->on('items')->nullOnDelete();
    });
}


};
