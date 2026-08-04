<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            // URL a donde apunta el QR (ej. ruta del wizard)
            $table->string('qr_url')->nullable()->after('venta_id');
            // Ruta pública del archivo QR (png/svg) guardado en /storage
            $table->string('qr_path')->nullable()->after('qr_url');
            // Ruta pública del PDF de etiqueta 4x8
            $table->string('label_path')->nullable()->after('qr_path');
        });
    }

    public function down(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropColumn(['qr_url','qr_path','label_path']);
        });
    }
};
