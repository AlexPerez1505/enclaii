<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('eventos', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->boolean('all_day')->default(false);
            $table->dateTime('end')->nullable();
            $table->string('repeat')->nullable();
            $table->dateTime('repeat_end')->nullable();
            $table->string('guests')->nullable();
            $table->string('alert')->nullable();
            $table->string('url')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down() {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn([
                'location', 'all_day', 'end', 'repeat', 'repeat_end',
                'guests', 'alert', 'url', 'notes'
            ]);
        });
    }
};
