<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendaPagamento extends Model
{
    use BelongsToEmpresa;

    protected $fillable = ['venda_id', 'forma_pagamento', 'valor', 'observacao'];

    protected function casts(): array
    {
        return ['valor' => 'decimal:2'];
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }
}
