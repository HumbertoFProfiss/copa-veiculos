<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContaReceber extends Model
{
    use BelongsToEmpresa;

    protected $table = 'contas_receber';

    protected $fillable = [
        'categoria_id', 'centro_custo_id', 'cliente_id', 'venda_id', 'descricao',
        'valor', 'vencimento', 'pagamento', 'status', 'recorrencia',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'vencimento' => 'date',
            'pagamento' => 'date',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaFinanceira::class, 'categoria_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }
}
