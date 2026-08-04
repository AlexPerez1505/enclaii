<?php
// app/Console/Commands/SendEventRemindersCommand.php
namespace App\Console\Commands;

use App\Jobs\SendEventReminderJob;
use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventRemindersCommand extends Command
{
    protected $signature = 'reminders:events';
    protected $description = 'Envía recordatorios de eventos (3d, 2d, 1d, hoy) usando plantillas de WhatsApp';

    public function handle(): int
    {
        $nowTz = Carbon::now(config('app.timezone'));
        $sendAt = config('services.whatsapp.reminder_send_at', '09:00');

        // Solo dispara a la hora configurada (exacto minuto)
        if ($nowTz->format('H:i') !== $sendAt) {
            $this->line("Skip (son las {$nowTz->format('H:i')}, espero {$sendAt})");
            return self::SUCCESS;
        }

        // Para cada offset
        foreach (['3d' => 3, '2d' => 2, '1d' => 1, '0d' => 0] as $when => $days) {
            $targetDay = $nowTz->copy()->addDays($days)->toDateString();

            // Busca eventos cuya FECHA de inicio sea ese día (independiente de la hora)
            Evento::whereDate('start', '=', $targetDay)
                ->select('id') // eficiencia
                ->chunk(200, function($chunk) use ($when) {
                    foreach ($chunk as $row) {
                        SendEventReminderJob::dispatch($row->id, $when)->onQueue('whatsapp');
                    }
                });

            $this->info("Programados recordatorios: {$when} (fecha: {$targetDay})");
        }

        return self::SUCCESS;
    }
}
