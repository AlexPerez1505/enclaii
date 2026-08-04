<?php
// database/migrations/2025_09_11_000002_add_media_and_fields_to_registros_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('registros', function (Blueprint $t) {
            if (!Schema::hasColumn('registros','tipo_equipo'))           $t->string('tipo_equipo')->nullable();
            if (!Schema::hasColumn('registros','subtipo_equipo'))        $t->string('subtipo_equipo')->nullable();
            if (!Schema::hasColumn('registros','subtipo_equipo_otro'))   $t->string('subtipo_equipo_otro')->nullable();
            if (!Schema::hasColumn('registros','numero_serie'))          $t->string('numero_serie')->nullable();
            if (!Schema::hasColumn('registros','marca'))                 $t->string('marca')->nullable();
            if (!Schema::hasColumn('registros','modelo'))                $t->string('modelo')->nullable();
            if (!Schema::hasColumn('registros','anio'))                  $t->string('anio', 4)->nullable();
            if (!Schema::hasColumn('registros','descripcion'))           $t->text('descripcion')->nullable();
            if (!Schema::hasColumn('registros','estado_actual'))         $t->unsignedTinyInteger('estado_actual')->nullable();
            if (!Schema::hasColumn('registros','fecha_adquisicion'))     $t->date('fecha_adquisicion')->nullable();
            if (!Schema::hasColumn('registros','ultimo_mantenimiento'))  $t->date('ultimo_mantenimiento')->nullable();
            // ¡Sin "proximo_mantenimiento" porque lo quitaste del UI!
            if (!Schema::hasColumn('registros','observaciones'))         $t->string('observaciones')->nullable();

            if (!Schema::hasColumn('registros','evidencia1'))            $t->string('evidencia1')->nullable();
            if (!Schema::hasColumn('registros','evidencia2'))            $t->string('evidencia2')->nullable();
            if (!Schema::hasColumn('registros','evidencia3'))            $t->string('evidencia3')->nullable();
            if (!Schema::hasColumn('registros','video'))                 $t->string('video')->nullable();
            if (!Schema::hasColumn('registros','documentoPDF'))          $t->string('documentoPDF')->nullable();
            if (!Schema::hasColumn('registros','firma_digital'))         $t->string('firma_digital')->nullable();
            if (!Schema::hasColumn('registros','user_name'))             $t->string('user_name')->nullable();
        });
    }

    public function down(): void {
        Schema::table('registros', function (Blueprint $t) {
            // no hacemos drop para no perder información; deja vacío o dropea solo si lo necesitas
        });
    }
};
