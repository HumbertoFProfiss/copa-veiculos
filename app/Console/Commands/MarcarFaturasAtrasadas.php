<?php

namespace App\Console\Commands;

use App\Services\Faturamento\GeradorFaturas;
use Illuminate\Console\Command;

class MarcarFaturasAtrasadas extends Command
{
    protected $signature = 'faturas:marcar-atrasadas';

    protected $description = 'Marca como atrasada toda fatura pendente com vencimento no passado';

    public function handle(GeradorFaturas $gerador): int
    {
        $total = $gerador->marcarAtrasadas();

        $this->info("{$total} fatura(s) marcada(s) como atrasada.");

        return self::SUCCESS;
    }
}
