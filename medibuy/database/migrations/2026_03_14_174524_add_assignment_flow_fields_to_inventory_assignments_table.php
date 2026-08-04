<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_assignments', 'folio')) {
                $table->string('folio')->nullable()->after('id');
            }

            if (!Schema::hasColumn('inventory_assignments', 'notes')) {
                $table->text('notes')->nullable()->after('quantity');
            }

            if (!Schema::hasColumn('inventory_assignments', 'status')) {
                $table->string('status', 30)->default('activa')->after('notes');
            }

            if (!Schema::hasColumn('inventory_assignments', 'return_reason')) {
                $table->text('return_reason')->nullable()->after('status');
            }

            if (!Schema::hasColumn('inventory_assignments', 'return_details')) {
                $table->text('return_details')->nullable()->after('return_reason');
            }

            if (!Schema::hasColumn('inventory_assignments', 'return_condition')) {
                $table->string('return_condition', 50)->nullable()->after('return_details');
            }

            if (!Schema::hasColumn('inventory_assignments', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('return_condition');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_assignments', function (Blueprint $table) {
            $columns = [
                'folio',
                'notes',
                'status',
                'return_reason',
                'return_details',
                'return_condition',
                'returned_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('inventory_assignments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};