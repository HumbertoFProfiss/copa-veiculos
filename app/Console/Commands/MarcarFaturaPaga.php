<?php

namespace App\Console\Commands;

use App\Models\Fatura;
use Illuminate\Console\Command;

/**
 * Baixa manual de fatura - ate existir gateway de pagamento real
 * (Stripe/Asaas/Pagar.me), a confirmacao de pagamento (PIX, boleto pago
 * fora do sistema etc) e registrada por quem opera a Copa Veiculos rodando
 * este comando, igual ja acontece com NF-e/notas manuais.
 */
class MarcarFaturaPaga extends Command
{
    protected $signature = 'faturas:marcar-paga {fatura : ID da fatura} {--forma=manual : Forma de pagamento (pix, boleto, manual...)}';

    protected $description = 'Marca uma fatura como paga (baixa manual)';

    public function handle(): int
    {
        $fatura = Fatura::withoutGlobalScopes()->find($this->argument('fatura'));

        if (! $fatura) {
            $this->error('Fatura não encontrada.');

            return self::FAILURE;
        }

        if ($fatura->status === 'paga') {
            $this->warn('Essa fatura já estava marcada como paga.');

            return self::SUCCESS;
        }

        $fatura->update([
            'status' => 'paga',
            'paga_em' => now(),
            'forma_pagamento' => $this->option('forma'),
        ]);

        $this->info("Fatura #{$fatura->id} ({$fatura->empresa->nome}, R$ ".number_format((float) $fatura->valor, 2, ',', '.').") marcada como paga.");

        return self::SUCCESS;
    }
}
