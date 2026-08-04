<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('users', function (Blueprint $table) {
      // Agrega el NIP si no existe
      if (!Schema::hasColumn('users', 'approval_pin_hash')) {
        $table->string('approval_pin_hash')->nullable()->after('password');
      }

      // Agrega 'role' solo si no existe (en tu caso ya existe y NO lo tocará)
      if (!Schema::hasColumn('users', 'role')) {
        $table->string('role')->default('user')->after('approval_pin_hash');
      }
    });
  }

  public function down(): void {
    Schema::table('users', function (Blueprint $table) {
      // Quita únicamente lo que agregamos seguro desde esta migración
      if (Schema::hasColumn('users', 'approval_pin_hash')) {
        $table->dropColumn('approval_pin_hash');
      }
      // OJO: no tocamos 'role' aquí para no borrar una columna previa del proyecto
    });
  }
};
