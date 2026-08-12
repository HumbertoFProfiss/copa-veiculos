<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustoVeiculo extends Model
{
    use BelongsToEmpresa;

    protected $table = 'custos_veiculo';

    protected $fillable = [
        'veiculo_id', 'categoria_id', 'fornecedor_id', 'descricao', 'valor', 'data', 'numero_nf', 'arquivo_nf',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data' => 'date',
        ];
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaFinanceira::class, 'categoria_id');
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }
}
