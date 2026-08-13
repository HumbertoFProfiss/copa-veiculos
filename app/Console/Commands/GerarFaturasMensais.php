<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Services\Faturamento\GeradorFaturas;
use Illuminate\Console\Command;

class GerarFaturasMensais extends Command
{
    protected $signature = 'faturas:gerar';

    protected $description = 'Gera a fatura do mes corrente pra toda empresa ativa (idempotente - nao duplica se rodar de novo)';

    public function handle(GeradorFaturas $gerador): int
    {
        $geradas = 0;

        Empresa::where('status', 'ativo')->each(function (Empresa $empresa) use ($gerador, &$geradas) {
            if ($gerador->gerarProximaFatura($empresa)) {
                $geradas++;
                $this->line("Fatura gerada: {$empresa->nome} ({$empresa->plano})");
            }
        });

        $this->info("{$geradas} fatura(s) gerada(s).");

        return self::SUCCESS;
    }
}
