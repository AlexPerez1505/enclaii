<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ModuloUso;
use Carbon\Carbon;

class LimpiarModulosUsados extends Command
{
    /**
     * El nombre y firma del comando (lo que escribirás en la terminal).
     */
    protected $signature = 'modulos:limpiar';

    /**
     * Descripción del comando para `php artisan list`.
     */
    protected $description = 'Elimina registros de módulos usados hace más de 12 horas';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        $limite = Carbon::now()->subHours(1);

        $cantidad = ModuloUso::where('updated_at', '<', $limite)->delete();

        $this->info("Se eliminaron {$cantidad} registros de módulos usados hace más de 12 horas.");
    }
}
