<?php

namespace App\Console;

use App\Console\Commands\EnviarRecordatoriosPagos;
use App\Console\Commands\RunAgenda;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Registra comandos de consola.
     */
    protected $commands = [
        EnviarRecordatoriosPagos::class,
        RunAgenda::class,

        // Otros comandos opcionales si existen en tu proyecto:
        // \App\Console\Commands\ReiniciarAsistencias::class,
        // \App\Console\Commands\NotificarPagosProximos::class,
        // \App\Console\Commands\LimpiarModulos::class,
    ];

    /**
     * Programa las tareas recurrentes de Laravel Scheduler.
     */
    protected function schedule(Schedule $schedule): void
    {
        $tz = config('app.timezone', 'America/Mexico_City');

        /*
        |--------------------------------------------------------------------------
        | Asistencias
        |--------------------------------------------------------------------------
        */
        $schedule->command('reiniciar:asistencias')
            ->twiceMonthly(1, 16)
            ->timezone($tz)
            ->withoutOverlapping();

        /*
        |--------------------------------------------------------------------------
        | Pagos próximos
        |--------------------------------------------------------------------------
        */
        $schedule->command('pagos:notificar-proximos')
            ->dailyAt('08:00')
            ->timezone($tz)
            ->withoutOverlapping();

        /*
        |--------------------------------------------------------------------------
        | Limpieza de módulos
        |--------------------------------------------------------------------------
        */
        $schedule->command('modulos:limpiar')
            ->hourly()
            ->timezone($tz)
            ->withoutOverlapping();

        /*
        |--------------------------------------------------------------------------
        | Recordatorios automáticos de pagos
        |--------------------------------------------------------------------------
        */
        $schedule->command('pagos:recordatorios')
            ->everyTenMinutes()
            ->withoutOverlapping(15)
            ->onOneServer()
            ->timezone($tz);

        /*
        |--------------------------------------------------------------------------
        | Inventario bajo
        |--------------------------------------------------------------------------
        */
        $schedule->command('inventory:check-low-stock')
            ->hourly()
            ->timezone($tz)
            ->withoutOverlapping();

        /*
        |--------------------------------------------------------------------------
        | Recordatorios de eventos de agenda
        |--------------------------------------------------------------------------
        | Este comando revisa eventos con next_reminder_at vencido y envía:
        | - Email
        | - WhatsApp
        | - Notificación interna
        |
        | Importante:
        | Para que funcione automático, el cron del servidor debe ejecutar:
        | * * * * * cd /ruta/de/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
        |--------------------------------------------------------------------------
        */
        $schedule->command('agenda:run --limit=200 --window=5')
            ->everyMinute()
            ->withoutOverlapping(5)
            ->onOneServer()
            ->timezone($tz)
            ->appendOutputTo(storage_path('logs/agenda-scheduler.log'));
    }

    /**
     * Carga comandos de consola.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Zona horaria global del scheduler.
     */
    protected function scheduleTimezone(): \DateTimeZone|string|null
    {
        return config('app.timezone', 'America/Mexico_City');
    }
}