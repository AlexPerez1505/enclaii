<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_components', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_components', 'equipment_id')) {
                $table->unsignedBigInteger('equipment_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('equipment_components', 'component_id')) {
                $table->unsignedBigInteger('component_id')->nullable()->after('equipment_id');
            }

            if (!Schema::hasColumn('equipment_components', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1)->after('component_id');
            }

            if (!Schema::hasColumn('equipment_components', 'condition')) {
                $table->string('condition')->nullable()->after('quantity');
            }

            if (!Schema::hasColumn('equipment_components', 'notes')) {
                $table->text('notes')->nullable()->after('condition');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipment_components', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('equipment_components', 'notes')) {
                $columns[] = 'notes';
            }

            if (Schema::hasColumn('equipment_components', 'condition')) {
                $columns[] = 'condition';
            }

            if (Schema::hasColumn('equipment_components', 'quantity')) {
                $columns[] = 'quantity';
            }

            if (Schema::hasColumn('equipment_components', 'component_id')) {
                $columns[] = 'component_id';
            }

            if (Schema::hasColumn('equipment_components', 'equipment_id')) {
                $columns[] = 'equipment_id';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};