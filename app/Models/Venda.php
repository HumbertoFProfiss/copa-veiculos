<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venda extends Model
{
    use BelongsToEmpresa, HasFactory;

    public const FORMAS_PAGAMENTO_LABELS = [
        'avista' => 'À vista',
        'dinheiro' => 'Dinheiro',
        'pix' => 'Pix',
        'transferencia' => 'Transferência',
        'cartao_debito' => 'Cartão de débito',
        'cartao_credito' => 'Cartão de crédito',
        'financiamento' => 'Financiamento',
        'consorcio' => 'Consórcio',
        'troca' => 'Troca',
        'misto' => 'Formas combinadas',
    ];

    protected $fillable = [
        'empresa_id', 'filial_id', 'veiculo_id', 'cliente_id', 'vendedor_id', 'forma_pagamento',
        'preco_venda', 'desconto', 'valor_entrada', 'valor_financiado', 'comissao_vendedor', 'status', 'data_venda',
        'data_entrega', 'prazo_garantia_dias', 'numero_contrato',
        'lucro_financeiro', 'instituicao_financeira', 'impostos',
    ];

    protected function casts(): array
    {
        return [
            'preco_venda' => 'decimal:2',
            'desconto' => 'decimal:2',
            'valor_entrada' => 'decimal:2',
            'valor_financiado' => 'decimal:2',
            'comissao_vendedor' => 'decimal:2',
            'data_venda' => 'date',
            'data_entrega' => 'date',
            'lucro_financeiro' => 'decimal:2',
            'impostos' => 'decimal:2',
        ];
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function filial(): BelongsTo
    {
        return $this->belongsTo(Filial::class);
    }

    public function parcelas(): HasMany
    {
        return $this->hasMany(ParcelaFinanciamento::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(VendaPagamento::class);
    }

    public function carroTroca(): HasMany
    {
        return $this->hasMany(CarroTroca::class);
    }

    public function garantiasChamados(): HasMany
    {
        return $this->hasMany(GarantiaChamado::class);
    }

    public function propostasFinanciamento(): HasMany
    {
        return $this->hasMany(PropostaFinanciamento::class);
    }

    public function notasFiscais(): HasMany
    {
        return $this->hasMany(NotaFiscal::class);
    }

    public function renaveTransferencias(): HasMany
    {
        return $this->hasMany(RenaveTransferencia::class);
    }
}
