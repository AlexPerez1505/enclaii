<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'nombre_comercial')) {
                $table->string('nombre_comercial')->nullable()->after('apellido');
            }

            if (!Schema::hasColumn('clientes', 'direccion')) {
                $table->text('direccion')->nullable()->after('email');
            }

            if (!Schema::hasColumn('clientes', 'activo')) {
                $table->boolean('activo')->default(true)->after('recibe_promocion');
            }

            if (!Schema::hasColumn('clientes', 'tipo_cliente')) {
                $table->string('tipo_cliente')->nullable()->after('activo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('clientes', 'nombre_comercial')) {
                $columnsToDrop[] = 'nombre_comercial';
            }

            if (Schema::hasColumn('clientes', 'direccion')) {
                $columnsToDrop[] = 'direccion';
            }

            if (Schema::hasColumn('clientes', 'activo')) {
                $columnsToDrop[] = 'activo';
            }

            if (Schema::hasColumn('clientes', 'tipo_cliente')) {
                $columnsToDrop[] = 'tipo_cliente';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};