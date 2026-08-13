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

    protected $fillable = [
        'empresa_id', 'filial_id', 'veiculo_id', 'cliente_id', 'vendedor_id', 'forma_pagamento',
        'preco_venda', 'desconto', 'comissao_vendedor', 'status', 'data_venda',
        'data_entrega', 'prazo_garantia_dias', 'numero_contrato',
    ];

    protected function casts(): array
    {
        return [
            'preco_venda' => 'decimal:2',
            'desconto' => 'decimal:2',
            'comissao_vendedor' => 'decimal:2',
            'data_venda' => 'date',
            'data_entrega' => 'date',
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

    public function carroTroca(): HasMany
    {
        return $this->hasMany(CarroTroca::class);
    }

    public function garantiasChamados(): HasMany
    {
        return $this->hasMany(GarantiaChamado::class);
    }
}
