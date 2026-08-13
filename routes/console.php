<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cobranca recorrente (ver App\Services\Faturamento) - precisa que o cron
// do servidor chame "php artisan schedule:run" a cada minuto pra isso
// disparar de verdade (o cron de producao hoje so roda queue:work).
Schedule::command('faturas:gerar')->monthlyOn(1, '06:00');
Schedule::command('faturas:marcar-atrasadas')->dailyAt('06:15');
