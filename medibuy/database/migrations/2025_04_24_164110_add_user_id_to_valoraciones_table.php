<?php
// Nueva migración para agregar user_id
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar la columna 'user_id' a la tabla 'valoraciones'
        Schema::table('valoraciones', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Eliminar la columna 'user_id' en caso de rollback
        Schema::table('valoraciones', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
