<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('wamid')->nullable()->index();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('from')->index();        // msisdn del cliente (E.164)
            $table->string('to')->nullable();       // tu número E.164
            $table->enum('direction', ['in','out']); // in=entrante, out=saliente
            $table->string('type')->nullable();     // text, image, document, etc.
            $table->text('text')->nullable();
            $table->string('media_id')->nullable();
            $table->string('media_link')->nullable();
            $table->string('media_filename')->nullable();
            $table->string('status')->nullable();   // sent, delivered, read, failed (para out)
            $table->timestamp('wa_timestamp')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('chat_messages');
    }
};
