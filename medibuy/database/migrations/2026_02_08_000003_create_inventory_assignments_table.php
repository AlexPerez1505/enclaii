<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('inventory_assignments', function (Blueprint $table) {
      $table->id(); // BIGINT

      // OJO: especificamos tabla exacta para evitar inferencias raras
      $table->foreignId('inventory_item_id')
        ->constrained('inventory_items')
        ->cascadeOnDelete();

      $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

      $table->unsignedInteger('quantity');
      $table->longText('signature')->nullable();
      $table->timestamp('assigned_at')->useCurrent();

      $table->timestamps();

      $table->index(['user_id', 'assigned_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('inventory_assignments');
  }
};
