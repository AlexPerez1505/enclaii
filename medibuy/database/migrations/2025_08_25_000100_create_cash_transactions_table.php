<?php
// database/migrations/2025_08_25_000100_create_cash_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
Schema::create('cash_transactions', function (Blueprint $table) {
    $table->id();
    $table->enum('type', ['allocation','disbursement','return']);
    $table->foreignId('manager_id')->constrained('users');
    $table->foreignId('counterparty_id')->constrained('users'); // jefa o usuario
    $table->decimal('amount', 12, 2);
    $table->string('purpose')->nullable();
    $table->json('evidence_paths')->nullable();
    $table->string('manager_signature_path')->nullable();
    $table->string('counterparty_signature_path')->nullable();
    $table->foreignId('nip_approved_by')->nullable()->constrained('users');
    $table->timestamp('nip_approved_at')->nullable();
    $table->string('pdf_receipt_path')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();

    // 🔎 Índices pensados para tus queries:
    // /transactions?manager_id=..&type=.. ordenado por fecha
    $table->index(['manager_id','type','created_at'], 'ct_mgr_type_date');
    // /transactions?user_id=..&type=.. ordenado por fecha
    $table->index(['counterparty_id','type','created_at'], 'ct_ctr_type_date');
    // listado general por fecha
    $table->index(['created_at'], 'ct_created');
});

  }
  public function down(): void {
    Schema::dropIfExists('cash_transactions');
  }
};
