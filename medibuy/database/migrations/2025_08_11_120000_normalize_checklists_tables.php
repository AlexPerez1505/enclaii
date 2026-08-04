<?php
// database/migrations/2025_08_11_120000_normalize_checklists_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    // Ingenieria
    Schema::table('checklist_ingenieria', function (Blueprint $table) {
      if (!Schema::hasColumn('checklist_ingenieria','verificados'))     $table->json('verificados')->nullable();
      if (!Schema::hasColumn('checklist_ingenieria','no_verificados'))  $table->json('no_verificados')->nullable();
      if (!Schema::hasColumn('checklist_ingenieria','componentes'))     $table->json('componentes')->nullable();
      if (!Schema::hasColumn('checklist_ingenieria','observaciones'))   $table->text('observaciones')->nullable();
      if (!Schema::hasColumn('checklist_ingenieria','evidencias'))      $table->json('evidencias')->nullable();
      if (!Schema::hasColumn('checklist_ingenieria','firma_responsable')) $table->string('firma_responsable')->nullable();
      if (!Schema::hasColumn('checklist_ingenieria','firma_supervisor'))  $table->string('firma_supervisor')->nullable();
    });

    // Embalaje
    Schema::table('checklist_embalaje', function (Blueprint $table) {
      if (!Schema::hasColumn('checklist_embalaje','verificados'))     $table->json('verificados')->nullable();
      if (!Schema::hasColumn('checklist_embalaje','no_verificados'))  $table->json('no_verificados')->nullable();
      if (!Schema::hasColumn('checklist_embalaje','componentes'))     $table->json('componentes')->nullable();
      if (!Schema::hasColumn('checklist_embalaje','observaciones'))   $table->text('observaciones')->nullable();
      if (!Schema::hasColumn('checklist_embalaje','evidencias'))      $table->json('evidencias')->nullable();
      if (!Schema::hasColumn('checklist_embalaje','firma_responsable')) $table->string('firma_responsable')->nullable();
      if (!Schema::hasColumn('checklist_embalaje','firma_supervisor'))  $table->string('firma_supervisor')->nullable();
    });

    // Entrega
    Schema::table('checklist_entrega', function (Blueprint $table) {
      if (!Schema::hasColumn('checklist_entrega','verificados'))     $table->json('verificados')->nullable();
      if (!Schema::hasColumn('checklist_entrega','no_verificados'))  $table->json('no_verificados')->nullable();
      if (!Schema::hasColumn('checklist_entrega','componentes'))     $table->json('componentes')->nullable();
      if (!Schema::hasColumn('checklist_entrega','observaciones'))   $table->text('observaciones')->nullable();
      if (!Schema::hasColumn('checklist_entrega','evidencias'))      $table->json('evidencias')->nullable();
      if (!Schema::hasColumn('checklist_entrega','firma_cliente'))   $table->string('firma_cliente')->nullable();
      if (!Schema::hasColumn('checklist_entrega','firma_entrega'))   $table->string('firma_entrega')->nullable();
    });
  }

  public function down(): void {
    // opcional: no hacemos drop por seguridad
  }
};
