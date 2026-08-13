<?php

namespace App\Services\Faturamento;

use App\Models\Empresa;
use App\Models\Fatura;
use Carbon\Carbon;

/**
 * Gera a fatura mensal de assinatura de uma empresa (ver App\Models\Fatura).
 * Idempotente: a unique(empresa_id, referencia) no banco garante que rodar
 * duas vezes no mesmo mes nao duplica - so retorna null na segunda vez.
 */
class GeradorFaturas
{
    public function gerarProximaFatura(Empresa $empresa, ?Carbon $referencia = null): ?Fatura
    {
        $referencia = ($referencia ?? now())->startOfMonth();

        if (Fatura::where('empresa_id', $empresa->id)->whereDate('referencia', $referencia)->exists()) {
            return null;
        }

        $valor = Empresa::PRECOS_PLANOS[$empresa->plano] ?? null;

        if ($valor === null) {
            return null;
        }

        return Fatura::create([
            'empresa_id' => $empresa->id,
            'plano' => $empresa->plano,
            'valor' => $valor,
            'referencia' => $referencia,
            'vencimento' => $referencia->copy()->addDays(9),
            'status' => 'pendente',
        ]);
    }

    public function marcarAtrasadas(): int
    {
        return Fatura::where('status', 'pendente')
            ->whereDate('vencimento', '<', now()->toDateString())
            ->update(['status' => 'atrasada']);
    }
}
