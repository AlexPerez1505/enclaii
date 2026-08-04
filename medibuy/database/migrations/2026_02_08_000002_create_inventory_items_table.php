<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('inventory_items', function (Blueprint $table) {
      $table->id(); // BIGINT

      $table->foreignId('inventory_category_id')
        ->constrained('inventory_categories')
        ->cascadeOnDelete();

      $table->string('name');
      $table->text('description')->nullable();

      $table->unsignedInteger('stock')->default(0);
      $table->unsignedInteger('stock_min')->default(0);
      $table->unsignedInteger('stock_max')->default(0);

      $table->json('characteristics')->nullable();
      $table->string('photo')->nullable();

      $table->timestamps();

      $table->index(['inventory_category_id', 'name']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('inventory_items');
  }
};
