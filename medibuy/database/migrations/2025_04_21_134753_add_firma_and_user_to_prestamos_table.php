<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->string('user_name')->nullable()->after('observaciones');
            $table->longText('firmaDigital')->nullable()->after('user_name');
            $table->enum('estado', ['activo', 'devuelto', 'retrasado', 'cancelado', 'vendido'])->default('activo')->change();
        });
    }
    
    public function down()
    {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->dropColumn(['user_name', 'firmaDigital']);
        });
    }
    
};
