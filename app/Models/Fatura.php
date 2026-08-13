<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

/**
 * Fatura de assinatura do proprio SaaS (Copa Veiculos cobrando a revenda
 * pelo plano contratado - nao confundir com contas a pagar/receber do
 * financeiro da revenda, que sao de outra tabela). Baixa manual por
 * enquanto (ver App\Console\Commands\MarcarFaturaPaga) - cobranca
 * automatica de cartao/boleto precisa de conta em gateway de pagamento
 * (Stripe/Asaas/Pagar.me), credencial externa que so o usuario pode
 * providenciar, mesma logica ja aplicada a NF-e/Renave/WhatsApp.
 */
class Fatura extends Model
{
    use BelongsToEmpresa;

    public const STATUS_LABELS = [
        'pendente' => 'Pendente',
        'paga' => 'Paga',
        'atrasada' => 'Atrasada',
        'cancelada' => 'Cancelada',
    ];

    protected $fillable = [
        'empresa_id', 'plano', 'valor', 'referencia', 'vencimento',
        'status', 'paga_em', 'forma_pagamento', 'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'referencia' => 'date',
            'vencimento' => 'date',
            'paga_em' => 'datetime',
        ];
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
