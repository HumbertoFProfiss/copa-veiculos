<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropostaFinanciamento extends Model
{
    use BelongsToEmpresa;

    protected $table = 'propostas_financiamento';

    public const STATUS_LABELS = [
        'simulada' => 'Simulada',
        'aprovada' => 'Aprovada (simulação)',
        'recusada' => 'Recusada (simulação)',
    ];

    protected $fillable = [
        'venda_id', 'banco_id', 'criado_por',
        'valor_financiado', 'entrada', 'num_parcelas', 'taxa_juros_am', 'valor_parcela', 'status',
    ];

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    /**
     * Tabela Price - PMT = PV * i / (1 - (1+i)^-n). Cálculo real; a taxa
     * usada é a cadastrada manualmente pra cada banco (sem API bancária).
     */
    public static function calcularParcela(float $valorFinanciado, float $taxaJurosAmPercentual, int $numParcelas): float
    {
        $i = $taxaJurosAmPercentual / 100;

        if ($i == 0.0) {
            return round($valorFinanciado / $numParcelas, 2);
        }

        $pmt = $valorFinanciado * $i / (1 - (1 + $i) ** -$numParcelas);

        return round($pmt, 2);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function statusVariant(): string
    {
        return match ($this->status) {
            'aprovada' => 'success',
            'recusada' => 'error',
            default => 'warning',
        };
    }
}
