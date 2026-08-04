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
        Schema::table('remisions', function (Blueprint $table) {
            $table->string('aplicar_iva')->nullable(); // o boolean si prefieres true/false
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remisions', function (Blueprint $table) {
            $table->dropColumn('aplicar_iva');
        });
    }
};
