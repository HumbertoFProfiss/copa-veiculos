<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcelaFinanciamento extends Model
{
    use BelongsToEmpresa;

    protected $table = 'parcelas_financiamento';

    protected $fillable = ['venda_id', 'numero_parcela', 'valor', 'vencimento', 'pagamento', 'status'];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'vencimento' => 'date',
            'pagamento' => 'date',
        ];
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }
}
