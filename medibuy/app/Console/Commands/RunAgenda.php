<?php

namespace App\Console\Commands;

use App\Jobs\SendAgendaReminderJob;
use App\Models\AgendaEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class RunAgenda extends Command
{
    protected $signature = 'agenda:run {--limit=200} {--window=5}';

    protected $description = 'Envía recordatorios pendientes de agenda por email y WhatsApp';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $window = max(1, (int) $this->option('window'));

        $now = now();
        $from = $now->copy()->subMinutes($window);

        $this->info("Buscando recordatorios entre {$from->toDateTimeString()} y {$now->toDateTimeString()}");

        Log::info('agenda:run iniciando', [
            'from' => $from->toDateTimeString(),
            'now' => $now->toDateTimeString(),
            'limit' => $limit,
            'window' => $window,
        ]);

        $events = AgendaEvent::query()
            ->whereNotNull('next_reminder_at')
            ->whereBetween('next_reminder_at', [$from, $now])
            ->where(function ($query) {
                $query->where('send_email', true)
                    ->orWhere('send_whatsapp', true);
            })
            ->where(function ($query) {
                $query->whereNull('completed')
                    ->orWhere('completed', false)
                    ->orWhere('completed', 0);
            })
            ->orderBy('next_reminder_at')
            ->limit($limit)
            ->get();

        $this->info("Eventos encontrados: {$events->count()}");

        Log::info('agenda:run eventos encontrados', [
            'count' => $events->count(),
        ]);

        foreach ($events as $event) {
            $this->line("Enviando recordatorio event_id={$event->id} - {$event->title}");

            try {
                Bus::dispatchSync(new SendAgendaReminderJob($event->id));

                Log::info('agenda:run recordatorio ejecutado', [
                    'event_id' => $event->id,
                    'title' => $event->title,
                ]);
            } catch (\Throwable $e) {
                Log::error('agenda:run error ejecutando recordatorio', [
                    'event_id' => $event->id,
                    'title' => $event->title,
                    'error' => $e->getMessage(),
                ]);

                $this->error("Error en evento {$event->id}: {$e->getMessage()}");
            }
        }

        $this->info('agenda:run terminado');

        Log::info('agenda:run terminado');

        return self::SUCCESS;
    }
}