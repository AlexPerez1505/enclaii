<?php
// database/migrations/2025_09_05_000002_create_reminder_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->string('to', 32);
            $table->enum('when', ['3d','2d','1d','0d']); // 3 días, 2 días, 1 día, hoy
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['evento_id','to','when']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
