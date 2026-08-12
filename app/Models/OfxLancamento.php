<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfxLancamento extends Model
{
    use BelongsToEmpresa;

    protected $table = 'ofx_lancamentos';

    protected $fillable = [
        'ofx_importacao_id', 'data', 'descricao', 'valor', 'tipo',
        'conciliado', 'contas_pagar_id', 'contas_receber_id',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'valor' => 'decimal:2',
            'conciliado' => 'boolean',
        ];
    }

    public function importacao(): BelongsTo
    {
        return $this->belongsTo(OfxImportacao::class, 'ofx_importacao_id');
    }

    public function contaPagar(): BelongsTo
    {
        return $this->belongsTo(ContaPagar::class, 'contas_pagar_id');
    }

    public function contaReceber(): BelongsTo
    {
        return $this->belongsTo(ContaReceber::class, 'contas_receber_id');
    }
}
