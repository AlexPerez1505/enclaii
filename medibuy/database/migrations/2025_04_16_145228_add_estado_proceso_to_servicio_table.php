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
        Schema::table('servicio', function (Blueprint $table) {
            $table->string('estado_proceso')->nullable(); // ← Ya no usamos after()
        });
    }
    
    public function down(): void
    {
        Schema::table('servicio', function (Blueprint $table) {
            $table->dropColumn('estado_proceso');
        });
    }
};
