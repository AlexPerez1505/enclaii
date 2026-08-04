<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class ReiniciarAsistencias extends Command
{
    protected $signature = 'reiniciar:asistencias';
    protected $description = 'Reinicia las asistencias y faltas de todos los empleados cada 1 y 16 del mes';

    public function handle()
    {
        $hoy = Carbon::now();

        // Si es día 1 o día 16 del mes, se reinician asistencias y faltas
        if ($hoy->day == 1 || $hoy->day == 16) {
            User::query()->update([
                'asistencias' => 0,
                'faltas' => 0,
            ]);

            $this->info('Asistencias y faltas reiniciadas correctamente.');
        } else {
            $this->info('Hoy no es día de reinicio.');
        }
    }
}
