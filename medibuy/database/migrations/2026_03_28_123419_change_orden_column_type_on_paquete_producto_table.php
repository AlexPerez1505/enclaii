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
        Schema::table('paquete_producto', function (Blueprint $table) {
            $table->unsignedInteger('orden')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paquete_producto', function (Blueprint $table) {
            $table->unsignedTinyInteger('orden')->default(0)->change();
        });
    }
};