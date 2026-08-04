<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_items', 'type')) {
                $table->string('type', 30)->default('activo_fijo')->after('name');
            }

            if (!Schema::hasColumn('inventory_items', 'asset_status')) {
                $table->string('asset_status', 30)->nullable()->after('type');
            }

            if (!Schema::hasColumn('inventory_items', 'brand')) {
                $table->string('brand')->nullable()->after('description');
            }

            if (!Schema::hasColumn('inventory_items', 'model')) {
                $table->string('model')->nullable()->after('brand');
            }

            if (!Schema::hasColumn('inventory_items', 'serial_number')) {
                $table->string('serial_number')->nullable()->after('model');
            }

            if (!Schema::hasColumn('inventory_items', 'asset_tag')) {
                $table->string('asset_tag')->nullable()->after('serial_number');
            }

            if (!Schema::hasColumn('inventory_items', 'condition')) {
                $table->string('condition', 30)->nullable()->after('asset_tag');
            }

            if (!Schema::hasColumn('inventory_items', 'unit')) {
                $table->string('unit', 50)->nullable()->after('stock_max');
            }

            if (!Schema::hasColumn('inventory_items', 'supplier')) {
                $table->string('supplier')->nullable()->after('unit');
            }

            if (!Schema::hasColumn('inventory_items', 'location')) {
                $table->string('location')->nullable()->after('supplier');
            }

            if (!Schema::hasColumn('inventory_items', 'notes')) {
                $table->text('notes')->nullable()->after('location');
            }
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_items', 'characteristics')) {
                $table->dropColumn('characteristics');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_items', 'characteristics')) {
                $table->json('characteristics')->nullable()->after('stock_max');
            }

            $cols = [
                'type',
                'asset_status',
                'brand',
                'model',
                'serial_number',
                'asset_tag',
                'condition',
                'unit',
                'supplier',
                'location',
                'notes',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('inventory_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
